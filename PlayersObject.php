<?php

/*
    Development Exercise

      The following code is poorly designed and error prone. Refactor the objects below to follow a more SOLID design.
      Keep in mind the fundamentals of MVVM/MVC and Single-responsibility when refactoring.

      Further, the refactored code should be flexible enough to easily allow the addition of different display
        methods, as well as additional read and write methods.

      Feel free to add as many additional classes and interfaces as you see fit.

      Note: Please create a fork of the https://github.com/BrandonLegault/exercise repository and commit your changes
        to your fork. The goal here is not 100% correctness, but instead a glimpse into how you
        approach refactoring/redesigning bad code. Commit often to your fork.

*/


# To implement 'Interface segregation principle', we are dividing the IReadWritePlayers into multiple smaller interfaces.

interface IReadPlayers {
	function readPlayers($source, $filename = null);
}

interface IWritePlayers {
	function writePlayer($source, $player, $filename = null);
}

interface IDisplayPlayers {
	function display($isCLI, $course);
}


# We need to implement single responsiblity for each class. Therefor, PlayersObject is refactored into three seperate classes.

# Class ReadPlayersObject: Read data from different sources for example, array, json, and file.


class ReadPlayersObject implements IReadPlayers {

 	/**
     * @param $source string Where we're retrieving the data from. 'json', 'array' or 'file'
     * @param $filename string Only used if we're reading players in 'file' mode.
     * @return string json
     */
    function readPlayers($source, $filename = null) {
        $playerData = null;

        switch ($source) {
            case 'array':
                $playerData = $this->getPlayerDataArray();
                break;
            case 'json':
                $playerData = $this->getPlayerDataJson();
                break;
            case 'file':
                $playerData = $this->getPlayerDataFromFile($filename);
                break;
        }

        if (is_string($playerData)) {
            $playerData = json_decode($playerData);
        }

        return $playerData;

    }

    function getPlayerDataArray() {

        $players = [];

        $jonas = new \stdClass();
        $jonas->name = 'Jonas Valenciunas';
        $jonas->age = 26;
        $jonas->job = 'Center';
        $jonas->salary = '4.66m';
        $players[] = $jonas;

        $kyle = new \stdClass();
        $kyle->name = 'Kyle Lowry';
        $kyle->age = 32;
        $kyle->job = 'Point Guard';
        $kyle->salary = '28.7m';
        $players[] = $kyle;

        $demar = new \stdClass();
        $demar->name = 'Demar DeRozan';
        $demar->age = 28;
        $demar->job = 'Shooting Guard';
        $demar->salary = '26.54m';
        $players[] = $demar;

        $jakob = new \stdClass();
        $jakob->name = 'Jakob Poeltl';
        $jakob->age = 22;
        $jakob->job = 'Center';
        $jakob->salary = '2.704m';
        $players[] = $jakob;

        return $players;

    }

    function getPlayerDataJson() {
        $json = '[{"name":"Jonas Valenciunas","age":26,"job":"Center","salary":"4.66m"},{"name":"Kyle Lowry","age":32,"job":"Point Guard","salary":"28.7m"},{"name":"Demar DeRozan","age":28,"job":"Shooting Guard","salary":"26.54m"},{"name":"Jakob Poeltl","age":22,"job":"Center","salary":"2.704m"}]';
        return $json;
    }

    function getPlayerDataFromFile($filename) {
        $file = file_get_contents($filename);
        return $file;
    }

}


# Class WritePlayersObject: Write data into different sources for example, array, json, and file.

class WritePlayersObject implements IWritePlayers {

    /**
     * @param $source string Where to write the data. 'json', 'array' or 'file'
     * @param $filename string Only used if we're writing in 'file' mode
     * @param $player \stdClass Class implementation of the player with name, age, job, salary.
     */
    function writePlayer($source, $player, $filename = null) {

    	$playersArray = [];
    	$playerJsonString = null;

        switch ($source) {
            case 'array':
                $this->playersArray[] = $player;
                return $playersArray;
                break;
            case 'json':
                $players = [];
                if ($this->playerJsonString) {
                    $players = json_decode($this->playerJsonString);
                }
                $players[] = $player;
                $this->playerJsonString = json_encode($player);
                return $playerJsonString;
                break;
            case 'file':
                $players = json_decode($this->getPlayerDataFromFile($filename));
                if (!$players) {
                    $players = [];
                }
                $players[] = $player;
                file_put_contents($filename, json_encode($players));
                break;
        }
    }

}


# Class DisplayPlayersObject: display() function is only for showing data. It does not matter, whether the data is coming from array, json, or a file. 

class DisplayPlayersObject implements IDisplayPlayers {


    function display($isCLI, $PlayersData) {

        if ($isCLI) {
            echo "Current Players: \n";
            foreach ($PlayersData as $player) {

                echo "\tName: $player->name\n";
                echo "\tAge: $player->age\n";
                echo "\tSalary: $player->salary\n";
                echo "\tJob: $player->job\n\n";
            }
        } else {

            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    li {
                        list-style-type: none;
                        margin-bottom: 1em;
                    }
                    span {
                        display: block;
                    }
                </style>
            </head>
            <body>
            <div>
                <span class="title">Current Players</span>
                <ul>
                    <?php foreach($players as $player) { ?>
                        <li>
                            <div>
                                <span class="player-name">Name: <?= $player->name ?></span>
                                <span class="player-age">Age: <?= $player->age ?></span>
                                <span class="player-salary">Salary: <?= $player->salary ?></span>
                                <span class="player-job">Job: <?= $player->job ?></span>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </body>
            </html>
            <?php
        }
    }

}



$ReadPlayersData = new ReadPlayersObject();
$ShowPlayersData = new DisplayPlayersObject();

# Reading data from array

$PlayersData = $ReadPlayersData->readPlayers('array');
$ShowPlayersData->display(php_sapi_name() === 'cli', $PlayersData);

# Reading data from 'playerdata.json' file

$PlayersData = $ReadPlayersData->readPlayers('file', 'playerdata.json');
$ShowPlayersData->display(php_sapi_name() === 'cli', $PlayersData);



?>