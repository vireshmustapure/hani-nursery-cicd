import os
import glob
import re

base_dir = r"c:\Users\Vidya\Downloads\New folder\admin"

# Directories to process
dirs_to_process = [
    base_dir,
    os.path.join(base_dir, "pages"),
    os.path.join(base_dir, "admin"),
    os.path.join(base_dir, "pages", "account")
]

ignore_files = {"index.php", "config.php", "db.php", "header.php", "footer.php", "PHPMailer.php"}

def fix_content(content, file_depth):
    # If the file was in root, it moves to root/folder/ (depth increases by 1)
    # If the file was in pages/, it moves to pages/folder/ (depth increases by 1)
    
    # Update includes: include '../config/db.php' -> include '../../config/db.php'
    # Any string containing '../' will need an extra '../' because we moved one level deeper.
    # Wait, simple replace:
    content = content.replace("'../", "'../../")
    content = content.replace('"../', '"../../')
    
    content = content.replace("include 'config/", "include '../config/")
    content = content.replace('include "config/', 'include "../config/')
    content = content.replace("include 'includes/", "include '../includes/")
    content = content.replace('include "includes/', 'include "../includes/')
    
    # Update .php references to folders
    # e.g., 'cart.php' -> '../cart/'
    # 'product.php?id=' -> '../product/?id='
    # This is tricky because we might replace things we shouldn't.
    # Let's use a regex to find all word.php occurrences
    def replace_php_link(match):
        name = match.group(1)
        if name in ignore_files or name.endswith('index.php'):
            return match.group(0)
        # e.g., cart.php -> ../cart/index.php
        return f"../{name}/index.php"
        
    content = re.sub(r'([a-zA-Z0-9_-]+)\.php', replace_php_link, content)
    
    return content

for d in dirs_to_process:
    if not os.path.exists(d): continue
    php_files = glob.glob(os.path.join(d, "*.php"))
    
    for fpath in php_files:
        basename = os.path.basename(fpath)
        if basename in ignore_files:
            continue
            
        folder_name = basename.replace(".php", "").strip()
        new_dir = os.path.join(d, folder_name)
        new_fpath = os.path.join(new_dir, "index.php")
        
        print(f"Moving {basename} to {new_dir}/index.php")
        
        # Read content
        with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
            content = file.read()
            
        # Create dir
        os.makedirs(new_dir, exist_ok=True)
        
        # Fix content
        new_content = fix_content(content, d)
        
        # Write to new path
        with open(new_fpath, "w", encoding="utf-8") as file:
            file.write(new_content)
            
        # Remove old file
        os.remove(fpath)

print("Done migration.")
