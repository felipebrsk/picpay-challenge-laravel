TODO List - overview

-   [x] create users table - associate the users with a polymorph relation to separate the customer and the shopkeeper;
-   [x] create the table of customers and shopkeepers - separate the document between users and user types (considering that each type of user can have different type of data structure and specifications);
-   [x] create the table of wallets - associate the wallets with the user to store the user balances and separate the users data and users balance;
-   [x] create the table of transaction tokens to store the tokens that are required to validate each transaction with the payee and payer - simple alternative to Google OTP (POC);
-   [x] create the table of transactions to store each transaction considering the payee and payer and the amount;
-   [x] establish each relation for the poc;
-   [x] implementar docker;
-   [x] create the endpoint to store the user;
-   [x] implement the authentication using JWT;
-   [x] create the endpoint to login;
-   [x] create a basic route to get the user data with wallet and userable;
-   [x] create route to store new transaction tokens - validate requirements: user can't be a shopkeeper, must have an existent and valid token and payee can't be the payer;
-   [x] create a route to get user transactions and to store transactions - validate requirements: user can't be a shopkeeper, must have an existent and valid token, should not have an existent recent transaction for the same payee and payee can't be the payer;
-   [x] create the notification for the users transaction - notify the payee and payer about the recent transaction - queue the notification;
-   [x] create the mailable for the users transaction - notify the payee and payer about the recent transaction - queue the mail;
-   [x] create a job to process the transaction balance update in queue - update the status for the transaction to approved on success;
-   [x] seeders and factories;
-   [x] add abstract service and repository - implement repository pattern as design pattern;
-   [x] provide tests for each endpoint and business rule;
-   [x] create the CI for tests;
-   [x] create an endpoint to cancel the transaction - chargeback payer and debit payee wallet;

Out of scope list

-   consider the negative transaction results: on chargeback, the payee may not have the balance in wallet;
-   if the transaction is not approved and the user cancel, but the queue lost the update balance, it'll subtract the value that did not come;
