# PHP Class Files Beautifier

Beautifies PHP code of specified locations.

## Usage
Run the `run.sh` file.

```bash
bash run.sh
```

## Requirements

- Pear
- Pear: Log  @see https://pear.php.net/package/Log/redirected
- Pear: Archive_Tar

If an error saying `Log.php` is not found, run
```bash
pear install Log
```

### Log
For the `Log` component, `Archive_Tar` is needed. And by itself, it is incompatible with PHP 7.2 or above. You may get a following error.
```
Fatal error: Cannot use result of built-in function in write context in /php/pear/Archive/Tar.php on line 639
```

### Tar
In that case, modify the file `/php/pear/Archive/Tar.php` and change the line 639
```php
$v_att_list = & func_get_args();
```
to
```php
$v_att_list = func_get_args();
```
Then run 
```bash
pear install Archive_Tar
```
You might need to install `Log` mentioned above as it could have been prevented by an error.
