[<img src="springinstituut.png" /> ](https://www.springinstituut.nl)

Course Format ROC2023 Plugin
====
```
This plugin shows a list related students / the practical scan of the selected student.
```

| |                                                      | |
| ---------------- |------------------------------------------------------| ----- |
| **Organisation** | [*Spring Instituut*](https://www.springinstituut.nl) | |
| **Author** | [*Peter Meint Heida*](mailto:info@heidaservices.nl)  | |
| **Type** | *format*                                             | |
| **Name** | *roc2023*                                            | |
| |                                                      | |

| Version | Releasedate | Short description                                                                             |
|---------|-------------|-----------------------------------------------------------------------------------------------|
| 3.02    | 2025122201  | Removed one line, removed a devider used in previous Moodle versions adn updated content.php |
| 3.01    | 2025082001  | Adapted code to Moodle 4.5                                                                    |
| 2.01    | 2024021901  | Adapted code to Moodle 4.3                                                                    |
| 1.1     | 2024011601  | Added picto for classical                                                                     |
| 1.0     | ?           | Initiële versie                                                                               |

:wrench: Settings
---
no settings

:bookmark_tabs: Releasenotes
---
| ReleaseNr  | File                                         | Function/Linenr           | Short description                                                                 | 
|------------|----------------------------------------------|---------------------------|-----------------------------------------------------------------------------------|
| 2026050101 | /classes/.../content/section.mustache        | 106-120                   | Changed handling of icon_exists because it was not working properly.              |
| 2025122201 | /templates/.../cm.mustache                   | 61                        | Removed line showing old diveder removed in current and higher versions of Moodle |
|            | /cls/output/courseformat/content.php         | *                         | Updated content corresponding with content of current buttons format file         | 
| 2025082001 | Complete new version based on format_buttons |                           |                                                                                   |
| 2024021901 | /templates/.../activity_info.mustache        | *                         | Adapted code to new lay-out                                                       |
|            | /templates/.../activity.mustache             | *                         | "                         "                                                       |
|            | /templates/.../cmname.mustache               | *                         | "                         "                                                       |
|            | /templates/.../cm.mustache                   | *                         | "                         "                                                       |
|            | /templates/.../completion.mustache           | *                         | "                         "                                                       |
|            | styles.css                                   | 'activity-item'           | Added a bit of styling for this class                                             | 
|            | /renderer.php                                | course_section_cm_roc2023 | Call to new lay-out files                                                         |
| 2024011601 | /pix/klassikaal.png                          | -                         | Added picto                                                                       |

:floppy_disk: Install
---

1. Copy the plugin directory "course/format/roc2023" into moodle\report\.
2. Check admin notifications to install.

:scroll: License
---

Released Under the GNU General Public Licence http://www.gnu.org/copyleft/gpl.html


