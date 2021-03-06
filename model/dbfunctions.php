<?php

//This function is called within view/php/games.php to generate the library list of games
function gamelist() {
    $selectgames = "SELECT * from game ORDER BY gameName;";
    include 'connect.php';
    $stmt = $conn->prepare($selectgames);
    $stmt->execute();
    return $staticresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function gamelisturl($gameID) {
    $selectgame = "SELECT gameID, REPLACE(gameName, ' ', '_') as gameName from game WHERE gameID='" . $gameID . "';";
    include 'connect.php';
    $stmt = $conn->prepare($selectgame);
    $stmt->execute();
    return $staticresult = $stmt->fetch(PDO::FETCH_ASSOC);
}

function mostRecentGame() {
    $selectgames = "SELECT * FROM game ORDER BY gameDate DESC LIMIT 1;";
    include 'connect.php';
    $stmt = $conn->prepare($selectgames);
    $stmt->execute();
    return $result = $stmt->fetch(PDO::FETCH_ASSOC);
}

function mostRecentFix() {
    $selectfix = "SELECT game.gameName, game.gameID, fixDateTime FROM fix 
    INNER JOIN game ON game.gameID = fix.gameID
    ORDER BY fixDateTime DESC LIMIT 1;";
    include 'connect.php';
    $stmt = $conn->prepare($selectfix);
    $stmt->execute();
    return $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
function mostRecentBlog() {
    $selectblog = "SELECT * FROM blog 
    ORDER BY datetime DESC LIMIT 1;";
    include 'connect.php';
    $stmt = $conn->prepare($selectblog);
    $stmt->execute();
    return $result = $stmt->fetch(PDO::FETCH_ASSOC);
}

//This function is called from 'view/php/individual_games.php'
function get_one_game($gameID) {
    $selectonegame = "SELECT * FROM game WHERE gameID='" . $_GET['gameID'] . "'";
    include 'connect.php';
    $stmt = $conn->prepare($selectonegame);
    $result = $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateRep($userID) {
    $update = "UPDATE rep_calcs SET 
    gameCommRep = (SELECT SUM(IFNULL(gameCommThUp,0)-IFNULL(gameCommThDown,0)) FROM gamecomm WHERE userID ='" . $userID . "'),
    gameReplyRep = (SELECT SUM(IFNULL(replyCommThUp,0)-IFNULL(replyCommThDown,0)) FROM gamereply WHERE userID ='" . $userID . "'),
    fixReplyRep = (SELECT SUM(IFNULL(fixReplyThUp,0)-IFNULL(fixReplyThDown,0)) FROM fixreply WHERE userID ='" . $userID . "'),
    fixCommRep = (SELECT SUM(IFNULL(fixCommThUp,0)-IFNULL(fixCommThDown,0)) FROM fixcomm WHERE userID ='" . $userID . "')
    WHERE userID ='" . $userID . "';";
    include 'connect.php';
    $stmt = $conn->prepare($update);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $update2 = "UPDATE user SET commRep = 
        (SELECT SUM(fixCommRep+fixReplyRep+gameCommRep+gameReplyRep) 
        FROM rep_calcs WHERE userID ='" . $userID . "')
        WHERE userID ='" . $userID . "';";
        include 'connect.php';
        $stmt = $conn->prepare($update2);
        $stmt->execute();
        return true;
    } else {
        return false;
    }
}

function updateRepFix($userID) {
    $update = "UPDATE user SET fixRep = 
    (SELECT SUM(fixThUp-fixThDown) FROM fix WHERE userID ='" . $userID . "')
    WHERE userID = '" . $userID . "';";
    include 'connect.php';
    $stmt = $conn->prepare($update);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

function updatethumbs($table, $thumb, $idtype, $id, $userID, $userID2, $thumbtype) {
    $update = "UPDATE ".$table." SET ".$thumb." = ".$thumb." + 1 WHERE ".$idtype." ='" . $id . "'";
    include 'connect.php';
    $stmt = $conn->prepare($update);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        if($userID == 'null') {
            $insert = "INSERT INTO thumbs_record (userID, thumbtype, ".$idtype.") VALUES (:userID, :thumb, :id);";
            include 'connect.php';
            $stmt = $conn->prepare($insert);
            $stmt->bindValue(':userID', $userID2);
            $stmt->bindValue(':thumb', $thumbtype);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            }else {
                return false;
            }
        } else if($table == "fix") {
            $update = "UPDATE user SET fixRep = 
            (SELECT SUM(IFNULL(fixThUp,0)-IFNULL(fixThDown,0)) FROM fix WHERE userID ='" . $userID . "')
            WHERE userID = '" . $userID . "';";
            include 'connect.php';
            $stmt = $conn->prepare($update);
            $stmt->execute();
//            if ($stmt->rowCount() > 0) {
            $insert = "INSERT INTO thumbs_record (userID, thumbtype, ".$idtype.") VALUES (:userID, :thumb, :id);";
            include 'connect.php';
            $stmt = $conn->prepare($insert);
            $stmt->bindValue(':userID', $userID2);
            $stmt->bindValue(':thumb', $thumbtype);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
//                return true;
//            } else {
//                return false;
//            }
        } else {
            $update = "UPDATE rep_calcs SET 
            gameCommRep = (SELECT SUM(IFNULL(gameCommThUp,0)-IFNULL(gameCommThDown,0)) FROM gamecomm WHERE userID ='" . $userID . "'),
            gameReplyRep = (SELECT SUM(IFNULL(replyCommThUp,0)-IFNULL(replyCommThDown,0)) FROM gamereply WHERE userID ='" . $userID . "'),
            fixReplyRep = (SELECT SUM(IFNULL(fixReplyThUp,0)-IFNULL(fixReplyThDown,0)) FROM fixreply WHERE userID ='" . $userID . "'),
            fixCommRep = (SELECT SUM(IFNULL(fixCommThUp,0)-IFNULL(fixCommThDown,0)) FROM fixcomm WHERE userID ='" . $userID . "')
            WHERE userID ='" . $userID . "';";
            include 'connect.php';
            $stmt = $conn->prepare($update);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $update2 = "UPDATE user SET commRep = 
                (SELECT SUM(fixCommRep+fixReplyRep+gameCommRep+gameReplyRep) 
                FROM rep_calcs WHERE userID ='" . $userID . "')
                WHERE userID ='" . $userID . "';";
                include 'connect.php';
                $stmt = $conn->prepare($update2);
                $stmt->execute();
                
                $insert = "INSERT INTO thumbs_record (userID, thumbtype, ".$idtype.") VALUES (:userID, :thumb, :id);";
                include 'connect.php';
                $stmt = $conn->prepare($insert);
                $stmt->bindValue(':userID', $userID2);
                $stmt->bindValue(':thumb', $thumbtype);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    return true;
                } else {
                    return false;
                }
//            updateRep('" . $userID . "');
//            insertThumb($userID2, $thumbtype, $idtype, $id;);
            
//        return true;
        } else {
            return false;
        }
    }
    } else {
        return false;
    }
}

function insertThumb($userID, $thumbtype, $idtype, $id) {
    $insert = "INSERT INTO thumbs_record (userID, thumbtype, ".$idtype.") VALUES (".$userID.", ".$thumbtype.", ".$id.");";
    include 'connect.php';
    $stmt = $conn->prepare($insert);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

function getuserThumbRecords($userID, $idtype, $id) {
    $select = "SELECT * FROM thumbs_record WHERE userID ='" . $userID . "' AND ".$idtype." = '" . $id . "';";
    include 'connect.php';
    $stmt = $conn->prepare($select);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//This function is to grab game comments and username
function getGameComments($gameid) {
    $comment = "SELECT user.username, user.avatarID, avatar.url, REPLACE(REPLACE(gameComment, char(12), '<br>'), CHAR(10), '<br>') as 'newgameComment', gameComment, gameCommDateTime, gameCommThUp, gameCommThDown, gamecomm.userID, gameID, gameCommID, deleted, user_deleted 
    FROM gamecomm
    INNER JOIN user ON user.userID = gamecomm.userID
    INNER JOIN avatar ON avatar.avatarID = user.avatarID
    WHERE gameID ='" . $gameid . "'
    ORDER BY gameCommDateTime DESC";
    include 'connect.php';
    $stmt = $conn->prepare($comment);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//This function grabs the 10 most recent comments
function get10RecentComments($gameid) {
    $comment = "SELECT user.username, user.avatarID, avatar.url, REPLACE(REPLACE(gameComment, char(12), '<br>'), CHAR(10), '<br>') as 'newgameComment', gameComment, gameCommDateTime, gameCommThUp, gameCommThDown, gamecomm.userID, gameID, gameCommID, deleted, user_deleted 
    FROM gamecomm
    INNER JOIN user ON user.userID = gamecomm.userID
    INNER JOIN avatar ON avatar.avatarID = user.avatarID
    WHERE gameID ='" . $gameid . "'
    ORDER BY gameCommDateTime DESC
    LIMIT 10;";
    include 'connect.php';
    $stmt = $conn->prepare($comment);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//Get total number of comments for a game
function getCommentCount($gameID) {
    $count = "SELECT COUNT(*) as 'count' FROM gamecomm WHERE gameID ='" . $gameID . "' AND user_deleted = false;";
    include 'connect.php';
    $stmt = $conn->prepare($count);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllCommentsReplies($gameID) {
    
}

//This function is to grab the game fixes and username
function getFixes($gameID) {
    $fix = "SELECT user.username, user.avatarID, avatar.url, REPLACE(REPLACE(fixInfo, char(12), '<br>'), CHAR(10), '<br>') as 'newfixInfo', fixInfo, fixDateTime, fixThUp, fixThDown, fix.userID, gameID, fixID, deleted, user_deleted
    FROM fix
    INNER JOIN user ON user.userID = fix.userID
    INNER JOIN avatar ON avatar.avatarID = user.avatarID
    WHERE gameID ='" . $gameID . "'
    AND user_deleted = false
    ORDER BY fixThUp DESC, fixThDown ASC;";
    include 'connect.php';
    $stmt = $conn->prepare($fix);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//This function grabs the top 3 liked fixes
function getTop3Fixes($gameID) {
    $fix = "SELECT user.username, user.avatarID, avatar.url, REPLACE(REPLACE(fixInfo, char(12), '<br>'), CHAR(10), '<br>') as 'newfixInfo', fixInfo, fixDateTime, fixThUp, fixThDown, fix.userID, gameID, fixID, deleted, user_deleted
    FROM fix
    INNER JOIN user ON user.userID = fix.userID
    INNER JOIN avatar ON avatar.avatarID = user.avatarID
    WHERE gameID ='" . $gameID . "'
    AND user_deleted = false
    ORDER BY fixThUp DESC, fixThDown ASC
    LIMIT 3;";
    include 'connect.php';
    $stmt = $conn->prepare($fix);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Get total number of fixes for a game
function getFixCount($gameID) {
    $count = "SELECT COUNT(*) as 'count' FROM fix WHERE gameID ='" . $gameID . "' AND user_deleted = false;";
    include 'connect.php';
    $stmt = $conn->prepare($count);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getBlogCommCount($blogID) {
    $count = "SELECT COUNT(*) as 'count' FROM blogcomm WHERE blogID ='" . $blogID . "' AND user_deleted = false;";
    include 'connect.php';
    $stmt = $conn->prepare($count);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//This function gets game reply comments
function getGameReply($gamecommID) {
    $comment = "SELECT user.username, user.avatarID, avatar.url, gameReplyID, REPLACE(REPLACE(replyComment, char(12), '<br>'), CHAR(10), '<br>') as 'newreplyComment', replyComment, replyCommDateTime, replyCommThUp, replyCommThDown, gamereply.userID, gameCommID, deleted, user_deleted 
    FROM gamereply
    INNER JOIN user ON user.userID = gamereply.userID
    INNER JOIN avatar ON avatar.avatarID = user.avatarID
    WHERE gameCommID ='" . $gamecommID . "'
    AND user_deleted = false
    ORDER BY replyCommDateTime DESC;";
    include 'connect.php';
    $stmt = $conn->prepare($comment);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getThumbs($userID) {
    $rep = "SELECT acctStatus, commRep, fixRep FROM user WHERE userID ='" . $userID . "'";
    include 'connect.php';
    $stmt = $conn->prepare($rep);
    $result = $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//This function is generic for inserting comments and replies
function insertComment($table, $data){
    include 'connect.php';
    if(!empty($data) && is_array($data)){
        $columns = '';
        $values  = '';
        $columnString = implode(',', array_keys($data));
        $valueString = ":".implode(',:', array_keys($data));
        $sql = "INSERT INTO ".$table." (".$columnString.") VALUES (".$valueString.")";
        $query = $conn->prepare($sql);
        print_r($data);
        foreach($data as $key=>$val){
            $query->bindValue(':'.$key, $val);
        }
        $insert = $query->execute();
    }
    return $insert;
}

//This function gets the number of replies
function getReplyNumber($commID){
    $reply = "SELECT COUNT(*) as 'replies', gameCommID 
    FROM gamereply
    WHERE gameCommID = '" . $commID . "'
    AND user_deleted = false;";
    include 'connect.php';
    $stmt = $conn->prepare($reply);
    $result = $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//This function gets the number of fix comments
function getFixCommentNumber($fixID) {
    $reply = "SELECT COUNT(*) as 'replies', fixID 
    FROM fixcomm
    WHERE fixID = '" . $fixID . "'
    AND user_deleted = false;";
    include 'connect.php';
    $stmt = $conn->prepare($reply);
    $result = $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getFixComments($fixID) {
    $comment = "SELECT user.username, user.avatarID, avatar.url, fixCommID, REPLACE(REPLACE(fixComment, char(12), '<br>'), CHAR(10), '<br>') as 'newfixComment', fixComment, fixCommDateTime, fixCommThUp, fixCommThDown, fixcomm.userID, fixID, deleted, user_deleted 
    FROM fixcomm
    INNER JOIN user ON user.userID = fixcomm.userID
    INNER JOIN avatar ON avatar.avatarID = user.avatarID
    WHERE fixID ='" . $fixID . "'
    AND user_deleted = false
    ORDER BY fixCommDateTime DESC";
    include 'connect.php';
    $stmt = $conn->prepare($comment);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFixCommReplyNumber($fixCommID) {
    $reply = "SELECT COUNT(*) as 'replies', fixcommID 
    FROM fixreply
    WHERE fixcommID = '" . $fixCommID . "'
    AND user_deleted = false;";
    include 'connect.php';
    $stmt = $conn->prepare($reply);
    $result = $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getFixReplies($fixCommID) {
    $comment = "SELECT user.username, user.avatarID, avatar.url, fixReplyID, REPLACE(REPLACE(fixReply, char(12), '<br>'), CHAR(10), '<br>') as 'newfixReply', fixReply, fixReplyDateTime, fixReplyThUp, fixReplyThDown, fixreply.userID, fixCommID, deleted, user_deleted 
    FROM fixreply
    INNER JOIN user ON user.userID = fixreply.userID
    INNER JOIN avatar ON avatar.avatarID = user.avatarID
    WHERE fixCommID ='" . $fixCommID . "'
    AND user_deleted = false
    ORDER BY fixReplyDateTime DESC";
    include 'connect.php';
    $stmt = $conn->prepare($comment);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getBlogComments($blogID) {
    $comment = "SELECT user.username, user.avatarID, avatar.url, blogcommID, REPLACE(REPLACE(comment, char(12), '<br>'), CHAR(10), '<br>') as 'newcomment', comment, datetime, blogcomm.userID, blogID, deleted, user_deleted 
    FROM blogcomm
    INNER JOIN user ON user.userID = blogcomm.userID
    INNER JOIN avatar ON avatar.avatarID = user.avatarID
    WHERE blogID ='" . $blogID . "'
    AND user_deleted = false
    ORDER BY datetime DESC";
    include 'connect.php';
    $stmt = $conn->prepare($comment);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getUsers() {
    $users = "SELECT count(*) as 'users' FROM user;";
    include 'connect.php';
    $stmt = $conn->prepare($users);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getGames() {
    $game = "SELECT count(*) as 'games' FROM game;";
    include 'connect.php';
    $stmt = $conn->prepare($game);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);  
}

function getFixesNo() {
    $fix = "SELECT count(*) as 'fixes' FROM fix;";
    include 'connect.php';
    $stmt = $conn->prepare($fix);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);  
}

function getComments() {
    $fix = "SELECT 
        (select count(*) from fixcomm)
        +
        (select count(*) from gamecomm)
        AS 'comms';";
    include 'connect.php';
    $stmt = $conn->prepare($fix);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);  
}

function getReplies() {
    $replies = "SELECT (select count(*) from fixreply)+(select count(*) from gamereply)
    AS 'replies';";
    include 'connect.php';
    $stmt = $conn->prepare($replies);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);  
}

function getAllUsers() {
    $users = "SELECT * FROM user ORDER BY username;";
    include 'connect.php';
    $stmt = $conn->prepare($users);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

function getOneUser($userID) {
    $user = "SELECT user.userID, user.username, avatar.avatarID, avatar.url, user.acctStatus, user.email 
    FROM user
    INNER JOIN avatar ON avatar.avatarID = user.avatarID 
    WHERE user.userID ='" . $userID . "';";
    include 'connect.php';
    $stmt = $conn->prepare($user);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); 
}

function getImages() {
    $image = "SELECT * FROM avatar;";
    include 'connect.php';
    $stmt = $conn->prepare($image);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllUserComments($table, $userID) {
    $comment = "SELECT * FROM ".$table."
    WHERE userID = '" . $userID . "';";
    include 'connect.php';
    $stmt = $conn->prepare($comment);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function delete_comm($table, $column, $id) {
    $delete = "UPDATE ".$table." SET deleted = true WHERE ".$column." = '" . $id . "';";
    include 'connect.php';
    $stmt = $conn->prepare($delete);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}
function undelete_comm($table, $column, $id) {
    $undelete = "UPDATE ".$table." SET deleted = false WHERE ".$column." = '" . $id . "';";
    include 'connect.php';
    $stmt = $conn->prepare($undelete);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}
function user_delete_comm($table, $column, $id) {
    $delete = "UPDATE ".$table." SET user_deleted = true WHERE ".$column." = '" . $id . "';";
    include 'connect.php';
    $stmt = $conn->prepare($delete);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

function change_status($type, $userID) {
    $status = "UPDATE user SET acctStatus = ".$type." WHERE userID = '" . $userID . "';";
    include 'connect.php';
    $stmt = $conn->prepare($status);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}
//This function sanitises input
function sanitise_input($input_string) {
    include 'connect.php';
    $input_string = trim($input_string);
    $input_string = stripslashes($input_string);
    $input_string = htmlspecialchars($input_string);
    $input_string = strip_tags($input_string);
    return $input_string;
}

function get_blogs() {
    $blogs = "SELECT username, blogID, blogTitle, datetime FROM blog
    INNER JOIN user on userID = authorID;";
    include 'connect.php';
    $stmt = $conn->prepare($blogs);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
function get_blog($blogID) {
    $blog = "SELECT username, blogID, blogTitle, blog, datetime FROM blog
    INNER JOIN user on userID = authorID
    WHERE blogID = '" . $blogID . "';";
    include 'connect.php';
    $stmt = $conn->prepare($blog);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); 
}
function get_blogurl($blogID) {
    $blog = "SELECT blogID, REPLACE(blogTitle, ' ', '_') as blogTitle FROM blog
    WHERE blogID = '" . $blogID . "';";
    include 'connect.php';
    $stmt = $conn->prepare($blog);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); 
}
function activityDate($gameID) {
    $date = "SELECT MAX(MaxDate) as 'date' FROM (
	SELECT MAX(fixDateTime) AS 'MaxDate' FROM fix WHERE fix.gameID = '" . $gameID . "'
	UNION
	SELECT MAX(gameCommDateTime) AS 'MaxDate' FROM gamecomm WHERE gamecomm.gameID = '" . $gameID . "'
	UNION
	SELECT MAX(fixReplyDateTime) AS 'MaxDate' FROM fixreply WHERE fixreply.gameID = '" . $gameID . "'
	UNION
    SELECT MAX(fixCommDateTime) AS 'MaxDate' FROM fixcomm WHERE fixcomm.gameID = '" . $gameID . "'
    UNION
    SELECT MAX(replyCommDateTime) AS 'MaxDate' FROM gamereply WHERE gamereply.gameID = '" . $gameID . "'
    ) as x;";
    include 'connect.php';
    $stmt = $conn->prepare($date);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function allCommentsReplies($gameID) {
    $count = "SELECT game.gameID, SUM(IFNULL(a.newcount,0)+IFNULL(b.newcount,0)+IFNULL(c.newcount,0)+IFNULL(d.newcount,0)) AS 'newcount'
    FROM game
    LEFT JOIN (
        SELECT gameID, COUNT(*) AS 'newcount'
        FROM gamecomm
        GROUP BY gameID) a ON a.gameID = game.gameID
    LEFT JOIN (
        SELECT gameID, COUNT(*) AS 'newcount'
        FROM gamereply
        GROUP BY gameID) b ON b.gameID = game.gameID
    LEFT JOIN (
        SELECT gameID, COUNT(*) AS 'newcount'
        FROM fixcomm
        GROUP BY gameID) c ON c.gameID = game.gameID
    LEFT JOIN (
        SELECT gameID, COUNT(*) AS 'newcount'
        FROM fixreply
        GROUP BY gameID) d ON d.gameID = game.gameID  
    WHERE game.gameID = '" . $gameID . "';";
    include 'connect.php';
    $stmt = $conn->prepare($count);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
