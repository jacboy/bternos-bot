# bternos-bot
Bternos (souɹǝʇq) is a bot that automatically flips the last tweet of another account

#features
- Gets last tweet of a person
- Checks, if it has already posted this tweet, if yes, if cancelles
- Flips text *and image* from the tweet
- Posts the generated tweet

#installation
First, you will need to create a twitter app with read and write access.
Then, make a new folder and copy all files from this repo into there.
Next, make a folder in the twitter bot folder called "twitter-api-php" and paste J7mbo's Twitter PHP API in there (https://github.com/J7mbo/twitter-api-php).
After that, open up "reply.php" (this is the main file of the bternos-bot) and replace the keys ("XXX") and the username, that it should take the tweets from (for example "vantezzen").
Now, just start up the bot by running reply.php. The bot must have read and write access to the files "lasttweet.txt" and "image.jpg". It is recommended to create a new cronjob that executes reply.php every minute.
