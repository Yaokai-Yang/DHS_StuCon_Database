<?php
    //SQL Access
    $dbServer = "localhost";
    $dbUsr = "clientAccess";
    $dbPsd = "Spamraam2!";
    $dbName = "compiled_research";

    $conn = mysqli_connect($dbServer, $dbUsr, $dbPsd, $dbName);

    if(!$conn)
    {
        echo 'Connection error: ' . mysqli_connect_error();
    }

    //Datamuse API
    $max = "5";
    $endpoint = "https://api.datamuse.com/words?ml="; 

    function getSynonyms($words)
    {
        global $max, $endpoint;
        $result = "";

        foreach ($words as $word)
        {
            $result .= $word;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$endpoint" . urlencode($word) ."&max=" . $max);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = json_decode(curl_exec($ch));
            curl_close($ch);

            foreach ($data as $key)
            {
                $result .= " ";
                $result .= $key->word;

                //DEBUG
                //print_r($key->word . "<br>");
            }

        }

        return $result;
    }

    function doSearch($query_arg)
    {
        //Do Query on SQL Server
        global $conn;

        if ($query_arg == "")
        {
            //Default case where nothing is given
            $sql = "SELECT * FROM `researched contentions`";
        }
        else
        {
            //Get JSON of synonyms from Thesaurus API
            $args = explode(" ", $query_arg);
            $query_arg = getSynonyms($args);
            $sql = "SELECT * FROM `researched contentions` WHERE Match(Info,Title) AGAINST('". $query_arg . "') LIMIT 0, 50";       //Displays first 50 results (Change later for multiple pages)
            //$sql = "SELECT MATCH(Info,Title) AGAINST('" . $query_arg . "') AS 'Score', `researched contentions`.* FROM `researched contentions` WHERE MATCH(Info,Title) AGAINST('" . $query_arg . "') LIMIT 0, 50";       //DEBUG TO SEE "SCORE"
        }

        $result = mysqli_query($conn, $sql);
        $contentions = mysqli_fetch_all($result, MYSQLI_ASSOC);

        //print_r($sql);                                        //DEBUG

        //Load all sources and addendums given contentions
        for ($x = 0; $x < count($contentions); $x++)
        {
            $sql = 'SELECT * FROM sources WHERE Contention_ID=' . $contentions[$x][ID];
            $result = mysqli_query($conn, $sql);
            $sources = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $contentions[$x] += ['Sources' => $sources];

            $sql = 'SELECT * FROM addendums WHERE Contention_ID=' . $contentions[$x][ID];
            $result = mysqli_query($conn, $sql);
            $notes = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $contentions[$x] += ['Notes' => $notes];
        }

        // echo '<pre>'; print_r($contentions); echo '</pre>';   //DEBUG

        return $contentions;
    }
?>  