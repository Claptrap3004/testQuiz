<?php

namespace quiz;

use Exception;

class CSVImporterStandard implements CanHandleCSV
{
    private DBFactory $dbFactory;

    private CanHandleDB $relationDBHandler;
    private Factory $factory;

    public function __construct()
    {
        $this->relationDBHandler = KindOf::RELATION->getDBHandler();
        $this->factory = Factory::getFactory();
        $this->dbFactory = DBFactory::getFactory();
    }


    function readCSV(string $fileName, string $separator = '@' ): void
    {
        $row = 0;
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            file_put_contents('logImport','opened file',8);
            while (($dataSet = fgetcsv($handle, 0, $separator)) !== FALSE) {
                $row++;
                if ($row === 1) continue;
                $this->proceedData($dataSet);
            }
        }

        fclose($handle);
    }

    private function proceedData(array $data): void
    {

        $category = $this->restoreLineBreaks($data[0]);
        $question = $this->restoreLineBreaks($data[1]);
        $explanation = $this->restoreLineBreaks($data[2]);;
        $answers = [];
        for ($i = 3; $i < count($data)-1; $i += 2) {
            $answers[$data[$i]] = (int)$data[$i + 1];

        }
        try {
            $this->dbFactory->createQuizQuestionByCSVImport($question, $category,$explanation, $answers);
        } catch (Exception $e) {
            echo $e;
            return;
        }
    }

    public function restoreLineBreaks(string $data):string
    {
        return str_replace('<br>', "\n",$data);
    }

    public function putLinebreaks(string $data): string
    {
       return str_replace("\n",'<br>', str_replace("\r", '<br>', $data));
    }
    function writeCSV(string $fileName, array $questionIds): void
    {
        // CSV-Inhalt im Speicher erstellen
        $output = fopen('php://output', 'wb');

        // Header setzen, um die Datei als Download anzubieten
        header('Content-Type: text/csv');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        // 'Content-Length' Header ist nicht notwendig, wenn die Datei direkt aus dem Speicher gesendet wird

        // CSV-Kopfzeile schreiben
        $description = 'Category,Question,Explanation,Answer,isRight,Answer,isRight,...';
        $val = explode(",", $description);
        fputcsv($output, $val, '@');

        // Daten schreiben
        foreach ($questionIds as $questionId) {
            try {
                $questionData = $this->factory->createQuizQuestionById($questionId);
            } catch (Exception $e) {
                continue; // Fehlerbehandlung
            }

            $preparedData = [];
            $preparedData[] = $this->putLinebreaks($questionData->getCategory()->getText());
            $preparedData[] = $this->putLinebreaks($questionData->getText());
            $preparedData[] = $this->putLinebreaks($questionData->getExplanation());

            $relationData = $this->relationDBHandler->findById($questionData->getId());
            foreach ($relationData as $relation) {
                $preparedData[] = $this->putLinebreaks($this->factory->findIdTextObjectById((int)$relation['answer_id'], KindOf::ANSWER)->getText());
                $preparedData[] = $this->putLinebreaks($relation['is_right']);
            }

            fputcsv($output, $preparedData, '@');
        }

        // Keine Notwendigkeit für fclose() hier, weil 'php://output' kein echtes Dateihandling benötigt
        fclose($output);
    }

//    function writeCSV(string $fileName, array $questionIds): void
//    {
//
//        $description = 'Category,Question,Explanation,Answer,isRight,Answer,isRight,...';
//
//        $fp = fopen($fileName, 'wb');
//        $val = explode(",", $description);
//        fputcsv($fp, $val,'@');
//        foreach ($questionIds as $questionId) {
//            try {
//                $questionData = $this->factory->createQuizQuestionById($questionId);
////                file_put_contents('testExport.log',"managed to Export question $questionId \n", FILE_APPEND);
//
//            } catch (Exception $e) {
////                file_put_contents('testExport.log',"failed to Export question $questionId \n", FILE_APPEND);
//                continue;
//            }
//            $preparedData = [];
//            $preparedData[] = $this->putLinebreaks($questionData->getCategory()->getText());
//            $preparedData[] = $this->putLinebreaks($questionData->getText());
//            $preparedData[] = $this->putLinebreaks($questionData->getExplanation());
//            $relationData = $this->relationDBHandler->findById($questionData->getId());
//            foreach ($relationData as $relation) {
//                $preparedData[] = $this->putLinebreaks($this->factory->findIdTextObjectById((int)$relation['answer_id'], KindOf::ANSWER)->getText());
//                $preparedData[] = $this->putLinebreaks($relation['is_right']);
//            }
//            fputcsv($fp, $preparedData,'@');
//        }
////         file_put_contents('testExport', $formattedData,FILE_APPEND);
//        fclose($fp);
//        header('Content-Type: text/csv');
//        header('Content-Description: File Transfer');
//        header('Content-Type: application/csv');
//        header('Expires: 0');
//        header('Cache-Control: must-revalidate');
//        header('Pragma: public');
//        header('Content-Disposition: attachment; filename=' . $fileName);
//        header('Content-Length: ' . filesize($fileName));
//
//    }
}