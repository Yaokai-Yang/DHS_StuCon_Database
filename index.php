<?php
    //DEBUG SECTION
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    //DEBUG SECTION END

    include_once 'includes/db_getter.php';

    if (isset($_POST['submit']) && $_POST['search'] != "")
    {
        $contentions = doSearch($_POST['search']);
        //echo '<script type = "text/javascript"> getDB(); </script>';
    }
    else
    {
        $contentions = doSearch('');                                 //DEFAULT SEARCH RESULTS, CHANGE LATER                   
        //echo '<script type = "text/javascript"> getDB(); </script>';
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Contentions DB</title>
        <link rel="stylesheet" href="style.css">

        <!-- Load in Results -->
        <script type = "text/javascript">
            
            var container;

            function getDB() 
            {
                container = document.getElementById('contentions_container');
                container.innerHTML = '';

                var data = <?= json_encode($contentions) ?>;

                for (var x = 0; x < data.length; x++)
                {
                    container.appendChild(createContention(data[x]));
                }
            }

            function createContention(contention)
            {
                let result = document.createElement("div");
                result.classList.add("contention");

                //Title
                let header = document.createElement("h1");
                header.innerText = contention.Title;
                result.appendChild(header);
                
                //Info
                let info = document.createElement("p");
                info.innerText = contention.Info;
                result.appendChild(info);

                //Source
                let sources = document.createElement("ul");
                contention.Sources.forEach((source)=>{
                    let li = document.createElement("li");
                    li.innerHTML = '<a href="' + source.Link + '" target="_blank""><i>' + source.Publisher + '</i>, ' + source.Date + '</a>';
                    sources.appendChild(li);
                })
                result.appendChild(sources);

                //Notes
                contention.Notes.forEach((addendum)=>{
                    let note = document.createElement("p2");
                    note.innerText = '[Note]: ' + addendum.Info;
                    result.appendChild(note);
                })

                return result;
            }
        </script>
    </head>
    
    <body onload="getDB()">
        <div id="headbar">
            <form action="" method="post">
                Search Box <input type="text" name="search"><br>
                <input type="submit" name="submit">
            </form>

            <form action="" method="post">
                <input type="submit" value="Reset">
            </form>
        </div>

        <div id="contentions_container">

        </div>
    </body>
</html>
