<?php

namespace quiz;

class DBHandlerProvider
{

    private static ?CanHandleDB $answerDBHandler = null;
    private static ?CanHandleDB $categoryDBHandler = null;
    private static ?CanHandleDB $questionDBHandler = null;
    private static ?CanHandleDB $relationDBHandler = null;
    private static ?CanHandleDB $statsDBHandler = null;
    private static ?CanHandleDB $userDBHandler = null;
    private static ?CanHandleQuizContent $quizContentDBHandler = null;

    /**
     * provides appropriate DBHandler depending on KindOf being ANSWER or CATEGORY, if any other KindOF element this
     * will return null
     */
    public static function getIdTextDBHandler(KindOf $kindOf): ?CanHandleDB
    {
        switch ($kindOf) {
            case KindOf::ANSWER :
                if (!self::$answerDBHandler) self::$answerDBHandler = new IdTextDBHandler($kindOf);
                return self::$answerDBHandler;
            case KindOf::CATEGORY :
                if (!self::$categoryDBHandler) self::$categoryDBHandler = new IdTextDBHandler($kindOf);
                return self::$categoryDBHandler;
            default:
                return null;
        }
    }


    /**
     * sets param idTextDBHandler to answer or category db handler depending on KindOf being ANSWER or CATEGORY,
     * if any other kindOf element is passed nothing changes
     */
    public static function setIdTextDBHandler(CanHandleDB $idTextDBHandler, KindOf $kindOf): void
    {
        switch ($kindOf) {
            case KindOf::ANSWER :
                self::$answerDBHandler = $idTextDBHandler;
                break;
            case KindOf::CATEGORY :
                self::$categoryDBHandler = $idTextDBHandler;
                break;
            default:
        }
    }

    public static function getQuestionDBHandler(): ?CanHandleDB
    {
        if (!self::$questionDBHandler) self::$questionDBHandler = new QuestionDBHandler(KindOf::QUESTION);
        return self::$questionDBHandler;
    }

    public static function setQuestionDBHandler(CanHandleDB $questionDBHandler): void
    {
        self::$questionDBHandler = $questionDBHandler;
    }

    public static function getRelationDBHandler(): ?CanHandleDB
    {
        if (!self::$relationDBHandler) self::$relationDBHandler = new RelationDBHandler(KindOf::RELATION);
        return self::$relationDBHandler;
    }

    public static function setRelationDBHandler(CanHandleDB $relationDBHandler): void
    {
        self::$relationDBHandler = $relationDBHandler;
    }

    public static function getStatsDBHandler(): ?CanHandleDB
    {
        if (!self::$statsDBHandler) self::$statsDBHandler = new StatsDBHandler(KindOf::STATS, $_SESSION['UserId']);
        return self::$statsDBHandler;
    }

    public static function setStatsDBHandler(CanHandleDB $statsDBHandler): void
    {
        self::$statsDBHandler = $statsDBHandler;
    }

    public static function getUserDBHandler(): ?CanHandleDB
    {
        if (!self::$userDBHandler) self::$userDBHandler = new UserDBHandler(KindOf::USER);

        return self::$userDBHandler;
    }

    public static function setUserDBHandler(CanHandleDB $userDBHandler): void
    {
        self::$userDBHandler = $userDBHandler;
    }

    public static function getQuizContentDBHandler(): CanHandleQuizContent|QuizContentDBHandler
    {
        if (!self::$quizContentDBHandler) self::$quizContentDBHandler = new QuizContentDBHandler(KindOf::QUIZCONTENT);

        return self::$quizContentDBHandler;
    }

    public static function setQuizContentDBHandler(CanHandleQuizContent $quizContentDBHandler): void
    {
        self::$quizContentDBHandler = $quizContentDBHandler;
    }


}