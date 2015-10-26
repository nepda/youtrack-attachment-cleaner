# YouTrack attachment cleaner

With this tiny script, you can batch-remove attachments from issues by query.

## Parameters

* `h|host` The youtrack host/URL
* `u|username` The login username
* `p|password` The login password
* `q|query` The search query for the issues
* `f|pattern` An optional filter/pattern for attachment filenames
* `l|limit` The limit/maximum of issues fetched, default is 100.

## Example

To delete the first 1000 matching resolved issues in project 'Test' with attachments, which are
created between 1990-01-01 and 2015-01-01.

    php src/cleaner.php \
        -h https://**.myjetbrains.com/youtrack \
        -u root -p '**secret**' \
        -q 'project: Test has: attachments created: 1990-01-01 .. 2015-01-01 State: Resolved' \
        --pattern 'backtrace|logfile' \
        --limit 1000

You can run this line until no results are found.
