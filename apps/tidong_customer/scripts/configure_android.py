from pathlib import Path
import xml.etree.ElementTree as ET
manifest = Path("android/app/src/main/AndroidManifest.xml")
ET.register_namespace("android", "http://schemas.android.com/apk/res/android")
root = ET.parse(manifest).getroot()
key = "{http://schemas.android.com/apk/res/android}name"
existing = {p.get(key) for p in root.findall("uses-permission")}
for name in ("android.permission.INTERNET", "android.permission.ACCESS_COARSE_LOCATION", "android.permission.ACCESS_FINE_LOCATION"):
    if name not in existing:
        node = ET.Element("uses-permission")
        node.set(key, name)
        root.insert(0, node)
ET.ElementTree(root).write(manifest, encoding="utf-8", xml_declaration=True)
