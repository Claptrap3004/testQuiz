<?php

namespace quiz;

use PDO;

class QuizContentDBHandler extends DataBase implements CanHandleQuizContent
{
    protected string $tableName;
    protected PDO $connection;

    /**
     * expects enum entry to contain appropriate table name
     * @param KindOf $kindOf
     */
    public function __construct(KindOf $kindOf)
    {

        $this->tableName = $kindOf->getTableName();
        $this->connection = $this->connect();
        $this->setTablename();
    }


    private function setTablename(): void
    {
        $factory = Factory::getFactory();
        $user = $factory->createUser($_SESSION['UserId']);
        $email = $user->getEmail();
        $email = str_replace('.', '_', $email);
        $email = str_replace('@', '_', $email);
        $this->tableName = $this->tableName . $email;
    }

    /**
     * creates new tables for tracking quizContent first and then populates quiz_content table with list of
     * question-ids setting first to actual
     * @param array $args
     * @return int
     */
    public function create(array $args): int
    {
        if (!$this->validateArgsCreate($args)) {
            if ($args === []) $this->createTables();
            return -1;
        }
        $this->createTables();
        $sql = "INSERT INTO $this->tableName (question_id,is_actual) VALUES (:question_id,:is_actual);";
        $stmt = $this->connection->prepare($sql);
        foreach ($args['question_ids'] as $index => $questionId) {
            $isActual = $index == 0 ? 1 : 0;
            $stmt->execute([':question_id' => $questionId, ':is_actual' => $isActual]);
        }
        return 1;
    }
    /**
     * provides data for all TextId objects or child class objects stored in db
     * filters array may include keys categoryIds or userIds storing int[] with appropriate ids but filtering only takes
     * place in certain child classes
     * @param array $filters
     * @return array
     */
    public function findAll(array $filters = []): array
    {
        $sql = "SELECT * FROM $this->tableName;";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * creates quiz_content and track_quiz_content tables for logged in user
     * @return void
     */
    public function createTables(): void
    {
        $track = $this->getTrackTableName();
        $sqls = [];
        $sqls[] = "DROP TABLE IF EXISTS $track;";
        $sqls[] = "DROP TABLE IF EXISTS $this->tableName;";
        $sqls[] = "CREATE TABLE $this->tableName (id INT PRIMARY KEY AUTO_INCREMENT,question_id INT,is_actual BOOL);";
        $sqls[] = "CREATE TABLE $track (id INT PRIMARY KEY AUTO_INCREMENT,content_id INT, answer_id INT);";
        foreach ($sqls as $sql) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
        }
    }

    private function getTrackTableName(): string
    {
        return 'track_' . $this->tableName;
    }

    // returns given answers to question if in quiz content

    /**
     * returns given answers to question if in quiz content table of logged-in user
     * return is numeric array containing associative arrays with key value pairs for id and text of answer
     * @param int $id
     * @return array
     */
    public function findById(int $id): array
    {
        $data = $this->findAll();
        $questions = [];
        foreach ($data as $item) $questions[] = $item['question_id'];
        if (!in_array($id, $questions)) return [];
        $table = $this->getTrackTableName();
        $sql = "SELECT * FROM $table WHERE content_id = :id;";
        $stmt = $this->connection->prepare($sql);
        $id = $this->getContentIdByQuestionId($id);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchALl(PDO::FETCH_ASSOC);
    }

    /**
     * since track_quiz_content table is related to content_id this method provides content_id if only question_id
     * is known
     * @param int $questionId
     * @return int
     */
    private function getContentIdByQuestionId(int $questionId):int
    {   $sql = "SELECT * FROM $this->tableName WHERE question_id = :question_id;";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':question_id' => $questionId]);
        return $stmt->fetch(2)['id'];
    }


    // expects array ['questionId' = int, 'answers' = [int]]

    /**
     * expects associative array containing keys 'question_id' as well as 'answers' where question id holds integer and
     * answers holding numeric array containing answers to track which answers the user did choose for given question
     * @param array $args
     * @return bool
     */
    public function update(array $args): bool
    {
        if ($this->validateArgsUpdate($args)) {
            $args['content_id'] = $this->getContentIdByQuestionId($args['question_id']);
            $table = $this->getTrackTableName();
            $sql = "DELETE FROM $table WHERE content_id = :id";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':id' => $args['content_id']]);
            foreach ($args['answers'] as $answerId) {
                $sql = "INSERT INTO $table (content_id,answer_id) VALUES (:content_id,:answer_id)";
                $stmt = $this->connection->prepare($sql);
                $success = $stmt->execute([':content_id' => $args['content_id'], ':answer_id' => $answerId]);
                if (!$success) return false;
            }
            return true;
        }
        return false;
    }



    // deletes single question from quiz content (also eventually given answers to that question)

    /**
     * deletes single question from quiz_content as well as eventually given answers to that question
     * @param int $id
     * @return bool
     */
    public function deleteAtId(int $id): bool
    {
        $track = $this->getTrackTableName();
        $sqls = [];
        $sqls[] = "DELETE FROM $track WHERE question_id = :id;";
        $sqls[] = "DELETE FROM $this->tableName WHERE question_id = :id;";
        foreach ($sqls as $sql) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':id' => $id]);
        }
        return true;
    }

    /**
     * returns associative array holding table data of quiz_content of actual question or false if no question
     * is set to actual
     * @return array|false
     */
    private function getActualData():array|false
    {
        $sql = "SELECT * FROM $this->tableName WHERE is_actual = 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(2);
    }

    /**
     * returns content_id of actual question or false if no question is set to actual
     * @return int|false|null
     */
    public function getActual():int|false|null
    {
        return $this->getActualData() != [] ? $this->getActualData()['id']: null;
    }

    /**
     * returns question_id of actual question or false if no question is set to actual
     * @return int|false|null
     */
    public function getActualQuestionId():int|false|null
    {
        return $this->getActualData()['question_id'] ?? null;
    }


    /**
     * selects question being actual (and all others not) according value of SetActual enum, value NONE sets all
     * questions to not actual to indicate end of quiz
     * @param SetActual $setActual
     * @return void
     */
    public function setActual(SetActual $setActual): void
    {
        $numberOfQuestions = count($this->findAll());
        $actual = $this->getActual();
        $newActual = match($setActual){
            SetActual::PREVIOUS => $actual > 1 ? $actual - 1 : 1,
            SetActual::NEXT => $actual <= $numberOfQuestions ? $actual + 1 : $numberOfQuestions,
            SetActual::FIRST => 1,
            SetActual::LAST => $numberOfQuestions,
            SetActual::NONE => $numberOfQuestions + 1
        };
        $sql = "UPDATE $this->tableName SET is_actual = 1 WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id'=>$newActual]);
        $sql = "UPDATE $this->tableName SET is_actual = 0 WHERE NOT id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id'=>$newActual]);

    }


    protected function validateArgsCreate(array $args): bool
    {
        return array_key_exists('question_ids', $args);
    }

    protected function validateArgsUpdate(array $args): bool
    {
        return array_key_exists('question_id', $args) && array_key_exists('answers', $args);
    }
}