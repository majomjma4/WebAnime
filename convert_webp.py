import os
from PIL import Image

directory = 'public/img'
if not os.path.exists(directory):
    print(f"Directory {directory} does not exist.")
    exit(1)

for filename in os.listdir(directory):
    if filename.endswith(".png"):
        filepath = os.path.join(directory, filename)
        webp_path = os.path.join(directory, filename[:-4] + ".webp")
        try:
            with Image.open(filepath) as img:
                img.save(webp_path, "WEBP", quality=85)
            print(f"Converted {filename} to .webp")
        except Exception as e:
            print(f"Error converting {filename}: {e}")
