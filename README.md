# UptimeRobot Twitter  Bot
I created this tool to post UptimeRobot alerts to twitter. Everything is customizable<br>
![alt text](https://i.imgur.com/Q345J02.png "Status")
<br>
<br>
<br>
[Live Example](https://twitter.com/NerdBaggyTest)


## Install
- [Download Latest Release Here](https://github.com/nerdbaggy/uptimerobot-twitter-bot/releases/latest)
- Unzip and upload to a web accessible folder
- Add a new alert in UptimeRobot in the 'Web-Hook' category<br>
For example if your script is at spencerl.com/twitterbot/urtb.php the ***URL to Notify*** will be spencerl.com/twitterbot/urtb.php?
- Configure twitter. See below

## Twitter
You need to make 4 keys from twitter to get this working. <br>
[Click Here for Directions](https://themepacific.com/how-to-generate-api-key-consumer-token-access-key-for-twitter-oauth/994/) <br>
Once you have the 4 keys put them in the ***config.php***

## Message
You can customize how the messages look when posting to Twitter. You can customize it to however you would like. Just needs to be below 140 chacters. But it can inculde emoji, ascii, links, hashtags, anything! You can customize the message with the variables below.

### Example

```
✔ {{host}} is now now up after {{downtime}} [{{timestamp}} {{timezone}}]
✖ {{host}} has gone down [{{timestamp}} {{timezone}}]
```

| Variable  | Example | Description |
| ------------- | ------------- | ------------- |
| {{host}}  | Website  | Name of the check |
| {{id}}  | 59426326  | ID of the check|
| {{status}} | Up | Status of the check|
| {{url}} | spencerl.com | Host of the check|
| {{details}} | Timeout | Why the check failed|
| {{downtime}} | 4 Minutes | How long a check was down for|
| {{timestamp}} | 05:24 PM | Current time|
| {{timezone}} | UTC | Timezone to get timestamp|
