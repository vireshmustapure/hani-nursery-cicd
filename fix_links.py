import os
import glob

base_dir = r"c:\Users\Vidya\Downloads\New folder\admin"

# Recursively find all PHP files
php_files = glob.glob(os.path.join(base_dir, "**", "*.php"), recursive=True)

for fpath in php_files:
    try:
        with open(fpath, "r", encoding="utf-8", errors="ignore") as file:
            content = file.read()
            
        original_content = content
        
        # Revert broken replacements
        content = content.replace("../db/index.php", "db.php")
        content = content.replace("../header/index.php", "header.php")
        content = content.replace("../footer/index.php", "footer.php")
        content = content.replace("../config/index.php", "config.php")
        content = content.replace("../index/index.php", "index.php")
        content = content.replace("../PHPMailer/index.php", "PHPMailer.php")
        
        if content != original_content:
            with open(fpath, "w", encoding="utf-8") as file:
                file.write(content)
            print(f"Fixed {fpath}")
    except Exception as e:
        print(f"Error processing {fpath}: {e}")

print("Done fixing links.")
