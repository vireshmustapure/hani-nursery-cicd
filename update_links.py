import os
import glob
import re

base_dir = r"c:\Users\Vidya\Downloads\New folder\admin"

# Files to process for replacing links (since we didn't move them)
files_to_process = [
    os.path.join(base_dir, "index.php"),
    os.path.join(base_dir, "includes", "header.php"),
    os.path.join(base_dir, "includes", "footer.php") # if exists
]

def fix_content(content):
    # Update .php references to folders
    def replace_php_link(match):
        name = match.group(1)
        if name in ["index", "config", "db", "header", "footer"]:
            return match.group(0)
        # e.g., pages/shop.php -> pages/shop/
        # e.g., blog-single.php?id= -> blog-single/?id=
        return f"{name}/"
        
    content = re.sub(r'([a-zA-Z0-9_-]+)\.php', replace_php_link, content)
    
    return content

for fpath in files_to_process:
    if not os.path.exists(fpath): continue
        
    print(f"Updating links in {fpath}")
    
    # Read content
    with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
        content = file.read()
        
    # Fix content
    new_content = fix_content(content)
    
    # Write to new path
    with open(fpath, "w", encoding="utf-8") as file:
        file.write(new_content)

print("Done link updates.")
