Things that we should do:
=========================

1) Add error checking for each find method. As of now, we have a lot of calls in a form of
```
$obj->find('a', 0)->text();
```

If ```find()``` fails, text will fail and take down the app

2) Implement DB checking. We don't need to clear out the database before each run. 
We should:
 - Check if class exists
   - If class exists, update it with latest information (if there are changes)
   - If class does not exist, add it
 - Same applies to professors
 
3) We should implement a database cleanup. If there are entries that are no longer applicable, they should be removed.