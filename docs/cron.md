# Cron

Add new cron job to crontab:

```
crontab –e
```

This opens vi editor for you. Create the cron command using the following syntax:
1. The number of minutes after the hour (0 to 59)
2. The hour in military time (24 hour) format (0 to 23)
3. The day of the month (1 to 31)
4. The month (1 to 12)
5. The day of the week(0 or 7 is Sun, or use name)
6. The command to run
More graphically they would look like this:
* * * * * Command to be executed
- - - - -
| | | | |
| | | | +----- Day of week (0-7)
| | | +------- Month (1 - 12)
| | +--------- Day of month (1 - 31)
| +----------- Hour (0 - 23)
+------------- Min (0 - 59)

An example command would be “`0 0 * * * /etc/cron.daily/script.sh`”. This
would mean that the shell script will exactly execute at midnight every
night. 