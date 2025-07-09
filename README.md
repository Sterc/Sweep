![MODX Extra by Heibel](https://img.shields.io/badge/Extra%20by-Heibel-310173.svg)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0)

# Sweep

**Sweep** is a MODX Extra that helps you **scan for and safely remove unused files** from your website. It provides a convenient way to identify which files are no longer referenced in your MODX content and clean them up directly from the interface.

---

## 🚀 Getting Started

1. Navigate to **Extras → Sweep**.
2. Click **`Scan files`** to begin analyzing the filesystem.
3. Go to the **Directories** tab and add the directories you want to scan.
   - All files in the selected directories will be checked for usage.

---

## 🔍 File Usage Detection

Sweep checks for file usage in the following MODX fields:

| Object                  | Fields Checked                             |
|------------------------|---------------------------------------------|
| `modResource`          | `content`, `introtext`, `description`, `properties` |
| `modChunk`             | `snippet`                                  |
| `modTemplate`          | `content`                                  |
| `modSnippet`           | `snippet`                                  |
| `modPlugin`            | `plugincode`                               |
| `modTemplateVarResource` | `value`                                 |
| `cgSetting`            | `value`                                    |
| `DigitalSignageSlides` | `data`                                     |

> ⚠️ **Warning:** If a file is used outside of these fields or objects, it may still be marked as *unused*. Please review files carefully before removal.

---

## 🧹 Deleting Files

You have several options for removing unused files:

- **Right-click on a file** and choose **Remove file**.
- **Select multiple files**, then right-click and remove them in bulk.
- Use the **Clean all** button to remove **all files marked as unused** in one click.

---

## 📁 Used Files Tab

The **Used files** tab displays all files that are currently in use on your site.

- You can see exactly **where each file is used**.
- This allows you to **identify large files still in use** and replace them manually with optimized versions if needed.

---

## 🛡 License

This project is licensed under the [GNU GPL v2 License](https://www.gnu.org/licenses/gpl-2.0).

---

Happy sweeping! 🧼
