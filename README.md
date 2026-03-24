**USAGE**:

`php ./bin/parser ./path/to/php/file.php <propertyName> <list of comma separated groups without spaces>`

**EX:**
```
php ./bin/parser ./examples/entity.php slug read,edit,list  # will add 3 groups
php ./bin/parser ./examples/entity.php slug edit            # will remove 'edit' group, but will preserve previous 2 groups
     
