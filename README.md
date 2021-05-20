# IOTANodeSharing
A simple WebAPI to search for IOTA Hornet- or Bee-Nodes for peering

**** 1. Usage of the website ****
To use the service, the following requirements have to be met:
1. The node's API must be accessible from the web.
2. The API's basicAuth is allowed to be enabled.
3. The API's excludeHealthCheckFromAuth-parameter is allowed to be set to false.

General Behaviour:
- To get peer-IDs as a result, the health-check of your node has to passed with one of the following HTTP-Codes: 200, 401, 503. Every peer-ID that is presented as a result is also health-checked every time!
- Already existing peers that fail the health-check in the matching process are sorted out and set to invailable. This status is removed at midnight (GMT+1). As long as a node is inavailable, it is not matched to any new node!
- If a node is not available for 7 consecutive days, it will be removed from the database!
- For the same node, new peer-IDs can only be received after 48 hours. If the request is redone within this timeframe, the same peer-IDs as before are presented.
- Entered information is compared to peerAdress and Port. If the Peering ID has changed (due to update of the node) or the E-Mail has changed, these values will be updated with a new request. WARNING: an empty E-Mail-Adress will clear the value in the database, if the node was already known.
- The matching process works with a FIFO-principle and takes the number of already matched nodes into account. The nodes with the least matches are preferred. If two nodes have the same amount of matches, the node that has been in the database for a longer time is preferred.
- If an E-Mail-Adress is provided, you will be informed every time, your nodes Peering-ID is presented to a new node! The E-Mail contains the Peering ID of the node your node was matched to.
- All sensitive data is stored AES-256-encrypted in the database. This goes for: peerAdress, port, apiPort, PeerID and e-Mail-Adress.


**** 2. Usage of the API ****
The API has a single endpoint: /getpeers.php

The following information has to be issued in JSON-Format via POST-request:
- [mandatory] "peerAdress": the adress of the node. This can be an IP-adress or a DNS-adress.
- [mandatory] "port": the node-port (normally 15600)
- [optional] "apiPort": the port of the nodes API. If empty, the API port will be set to 14265. During the API-call, a health-check is executed to the following: peerAdress:apiPort/health | https://peerAdress/health | https://peerAdress/api/health
- [mandatory] "peerID": the peering ID, that can be found on the nodes dashboard
- [mandatory] "network": the network, the node is operating in. The possible values are: testnet, mainnet, comnet, devnet
- [optional]  "eMail": a valid eMail-Adress
- [optional]  "healthCheck": can be set to "false" to get peers without an additional health check

A successful request returns up to 3 peering-IDs from other nodes in as objects in a "records"-object.
An unsuccessful request returns a single "message" information.
