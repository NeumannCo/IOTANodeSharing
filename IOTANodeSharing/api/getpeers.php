<?php
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // get database connection
    include '../config/database.php';

    include 'sendmail.php';
    
    // instantiate peers object
    include 'class_peers.php';
    include '../config/class_encryption.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $peers = new Peers($db);
    
    // get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // make sure data is not empty
    if(
        !empty($data->peerAdress) &&
        !empty($data->port) &&
        !empty($data->apiPort) &&
        !empty($data->peerID) &&
        !empty($data->network)
    ){

        // set peers property values
        $peers->peerAdress = str_replace("https://", "", str_replace("http://", "", $data->peerAdress));
        $peers->port = $data->port;
        $peers->apiPort = $data->apiPort;
        $peers->peerID = $data->peerID;
        if(!empty($data->eMail)){
            $peers->eMail = $data->eMail;
        } else {
            $peers->eMail = "";
        }
        $peers->dateAdded = date('Y-m-d H:i:s');
        $peers->availability = 1;
        $peers->lastAvailable = date('Y-m-d H:i:s');
        $peers->network = $data->network;
        if(!empty($data->healthCheck)){
            $healthCheck = "true";
        } else {
            $healthCheck = $data->healthCheck;
        }

        if($peers->healthCheck() == 200 || $peers->healthCheck() == 503 || $peers->healthCheck() == 401) {
        
            if(!$peers->exists()) {
                $peers->create();
            }

            $recentlyMatched = $peers->getRecentMatches();
            if($recentlyMatched->rowCount() > 0) {
                
                // recently matched nodes are presented for 48 hours before new matches are selected

                $encryption = new Encryption();

                // peers array
                $peers_arr=array();
                $peers_arr["records"]=array();

                while ($row = $recentlyMatched->fetch(PDO::FETCH_ASSOC)){
                    $peers_item = "";
                    extract($row);

                    // when the given adress is not an IP-Adress but a DNS, the long ID for peering slightly differs
                    if(filter_var($encryption->decryptify($PeerAdress), FILTER_VALIDATE_IP)) {
                        $peers_item = "/ip4/";
                    } else {
                        $peers_item = "/dns/";
                    }

                    $peers_item .= $encryption->decryptify($PeerAdress) . "/tcp/" . $encryption->decryptify($Port) . "/p2p/" . $encryption->decryptify($PeerID);
                    $peers_item_array = array("peerID" => $peers_item);
            
                    array_push($peers_arr["records"], $peers_item_array);
                }
                        
                // set response code - 200 OK
                $resultHttpCode = 200;
            
                // show peers data in json format
                $resultMsg = json_encode($peers_arr);

            } else {
                // new peers are only selected, if no recent matches (<48 hours) are available to display

                $peersHealthy = false;

                do {
                    // query peers
                    $stmt = $peers->read();
                    $peersFound = $stmt->rowCount();
                    
                    // check if more than 0 record found
                    if($peersFound > 0){
                    
                        $encryption = new Encryption();

                        // peers array
                        $peers_arr=array();
                        $peers_arr["records"]=array();
                        $peersToMatch = array();
                    
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $peers_item = "";

                            // extract row
                            // this will make $row['name'] to
                            // just $name only
                            extract($row);
                            
                            $peerToCheck = new Peers($db);
                            $peerToCheck->peerAdress = $encryption->decryptify($PeerAdress);
                            $peerToCheck->apiPort = $encryption->decryptify($APIPort);
                            $peerToCheck->port = $encryption->decryptify($Port);

                            if($healthCheck == "false") {

                                $peersHealthy = true;

                                // when the given adress is not an IP-Adress but a DNS, the long ID for peering slightly differs
                                if(filter_var($$encryption->decryptify($PeerAdress), FILTER_VALIDATE_IP)) {
                                    $peers_item = "/ip4/";
                                } else {
                                    $peers_item = "/dns/";
                                }

                                $peers_item .= $encryption->decryptify($PeerAdress) . "/tcp/" . $encryption->decryptify($Port) . "/p2p/" . $encryption->decryptify($PeerID);
                                $peers_item_array = array("peerID" => $peers_item);

                                // save selected peers for later match
                                $peersToMatch_item = array("ID" => $ID, "eMail" => $encryption->decryptify($eMail));
                                array_push($peersToMatch, $peersToMatch_item);

                                array_push($peers_arr["records"], $peers_item_array);

                            } elseif($peerToCheck->healthCheck() == 200 || $peerToCheck->healthCheck() == 503 || $peerToCheck->healthCheck() == 401) {
                            
                                $peersHealthy = true;
                                $peerToCheck->updateAvailability();

                                // when the given adress is not an IP-Adress but a DNS, the long ID for peering slightly differs
                                if(filter_var($$encryption->decryptify($PeerAdress), FILTER_VALIDATE_IP)) {
                                    $peers_item = "/ip4/";
                                } else {
                                    $peers_item = "/dns/";
                                }

                                $peers_item .= $encryption->decryptify($PeerAdress) . "/tcp/" . $encryption->decryptify($Port) . "/p2p/" . $encryption->decryptify($PeerID);
                                $peers_item_array = array("peerID" => $peers_item);
                                
                                // save selected peers for later match
                                $peersToMatch_item = array("ID" => $ID, "eMail" => $encryption->decryptify($eMail));
                                array_push($peersToMatch, $peersToMatch_item);
                        
                                array_push($peers_arr["records"], $peers_item_array);

                            } else {
                                $peersHealthy = false;
                                $peerToCheck->disable();
                                break;
                            }
                        }
                    
                        // set response code - 200 OK
                        $resultHttpCode = 200;
                    
                        // show peers data in json format
                        $resultMsg = json_encode($peers_arr);
                    } else { 
                
                        // set response code - 404 Not found
                        $resultHttpCode = 404;
                    
                        // tell the user no peers found
                        $resultMsg = json_encode(array("message" => "No peers found."));
                    }
                } while ($peersHealthy == false && $peersFound > 0);

                if($peersFound == 0) {
                    $resultHttpCode = 404;
                    $resultMsg = json_encode(array("message" => "No peers found that passed the health check or have not been already matched with your node."));
                } else {
                    foreach($peersToMatch as $item) {
                        $peers->match($item["ID"]);

                        if($item["eMail"] != "") {
                            if(filter_var($peers->peerAdress, FILTER_VALIDATE_IP)) {
                                $matchedID = "/ip4/";
                            } else {
                                $matchedID = "/dns/";
                            }

                            $matchedID .= $peers->peerAdress . "/tcp/" . $peers->port . "/p2p/" . $peers->peerID;
                            sendmail($item["eMail"], $matchedID);
                        }
                    }
                }
            }

            http_response_code($resultHttpCode);
            echo $resultMsg;

        } else {
            // set response code - 503 service unavailable
            http_response_code(503);
        
            // tell the user
            echo json_encode(array("message" => "Unable to create peer. Node failed the health check."));
        }
    }
    
    // tell the user data is incomplete
    else{
    
        // set response code - 400 bad request
        http_response_code(400);
    
        // tell the user
        echo json_encode(array("message" => "Unable to create peer. Data is incomplete."));
    }

?>