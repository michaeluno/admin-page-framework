# Downloads a file
# Example:
# download http://codeception.com/codecept.phar /tmp/codecept.phar
download() {
    
    # If the file size is more than 0 byte, do not download.
    if [[ $(find "$2" -type f -size +0c 2>/dev/null) ]]; then
        echo "Download: Using the cached file."
        return
    fi    

    if [ `which curl` ]; then
        # Not sure why but enclosing paths in quotes results in an empty file
        curl -s $1 > $2
        
        # Sometimes curl fails to fill the file contents although it creates a file.
        if [[ ! $(find "$1" -type f -size +0c 2>/dev/null) ]]; then
            # Try with wget as the above function is default to curl
            echo Could not fill the file. Now trying with wget.
            wget -nv -O "$2" "$1" --no-check-certificate 1> NUL 2> NUL
        fi 
        
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1" --no-check-certificate 1> NUL 2> NUL
    fi        
    
}
