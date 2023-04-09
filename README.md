
**API to Manage Telegram Bot Users**

Using the telegram bot API, we can get users that send a message to the bot and then save their info to the system
Also send a message to channel/group which bot is added to as an admin. Adding a bot to a channel is done manually via Telegram

Kindly Note:
subscribe users to a chat bot 
- user is not subscribed to chat bot
- user is sent message from bot

subscribe users to channel
- a bot can not add a user automatically to a channel at the moment, according to the telegram docs
- a user can use an invite link to join a channel or group
- the application provides endpoint to create invitation link
- updates are received automatically on the application when a user posts on the group
- and finally, a user can be invited to chat with a bot using a bot invite link e.g  https://t.me/{botusername}
- updates are also received automatically on the application when the user interacts with a bot


- after they join, an update is sent to the webhook, which in turn allows the app to save the user's info


**Pre-setup**

Create Telegram Bot via Telegram app using botfather: Link here ...
Add bot token to env file with key: TELEGRAM_BOT_TOKEN

Add Bot to Channel via Telegram

Application Features
Users that have sent message to bot are automatically saved to the application via webhook

**Available Endpoints**
 - /users  
 - /users/{id} 
 - /botinfo
 - /getupdates
 - /sendmessage
 - /sendmessagetoall
 - /invitelink
 - /setwebhook
 - /webhookinfo
 - /deletewebhook


API Documentation
http://localhost:8000/api/documentation
