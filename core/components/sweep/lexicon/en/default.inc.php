<?php

include_once 'setting.inc.php';

$_lang['sweep'] = 'Sweep';
$_lang['sweep_menu_desc'] = 'MODX Unused Files Cleaner.';
$_lang['sweep_intro_msg'] = 'This tool scans your MODX site for uploaded files that are not used in any content, TVs, chunks, or other core components.';
$_lang['sweep_intro_used_msg'] = 'Here you can find used files and where they used.';
$_lang['sweep_intro_directories_msg'] = 'Here you can specify which directories should be scanned to find unused files. The path should be relative to the base path of the site.';

$_lang['sweep_items'] = 'Files not in use';
$_lang['sweep_used'] = 'Used files';
$_lang['sweep_directories'] = 'Directories';
$_lang['sweep_item_id'] = 'Id';
$_lang['sweep_item_name'] = 'Name';
$_lang['sweep_item_description'] = 'Description';
$_lang['sweep_item_path'] = 'Path';
$_lang['sweep_item_usedin'] = 'Used in';
$_lang['sweep_item_size'] = 'Size';
$_lang['sweep_item_active'] = 'Active';

$_lang['sweep_item_create'] = 'Create file';
$_lang['sweep_item_scan'] = 'Scan files';
$_lang['sweep_item_scan_complete'] = 'Scan complete';
$_lang['sweep_item_scan_error'] = 'Scan error';
$_lang['sweep_item_update'] = 'Update file';
$_lang['sweep_item_enable'] = 'Enable file';
$_lang['sweep_items_enable'] = 'Enable files';
$_lang['sweep_item_disable'] = 'Disable file';
$_lang['sweep_items_disable'] = 'Disable files';
$_lang['sweep_item_archive'] = 'Archive file';
$_lang['sweep_items_archive'] = 'Archive files';
$_lang['sweep_item_remove'] = 'Remove file';
$_lang['sweep_items_remove'] = 'Remove selected files';
$_lang['sweep_items_remove_all'] = 'Clean all';
$_lang['sweep_item_remove_confirm'] = 'Are you sure you want to remove this file?';
$_lang['sweep_items_remove_confirm'] = 'Are you sure you want to remove this files?';
$_lang['sweep_items_remove_all_confirm'] = 'Are you sure you want to remove all files marked as unused?';

$_lang['sweep_item_err_name'] = 'You must specify the name of file.';
$_lang['sweep_item_err_ae'] = 'A file already exists with that name.';
$_lang['sweep_item_err_nf'] = 'File not found.';
$_lang['sweep_item_err_ns'] = 'File not specified.';
$_lang['sweep_item_err_file_remove'] = 'Could not remove file.';
$_lang['sweep_item_err_file_nf'] = 'File not found on disk. Removing record in database.';
$_lang['sweep_item_err_remove'] = 'An error occurred while trying to remove the file.';
$_lang['sweep_item_err_save'] = 'An error occurred while trying to save the file.';

$_lang['sweep_grid_search'] = 'Search';
$_lang['sweep_grid_actions'] = 'Actions';

$_lang['sweep_total_found'] = 'Found';
$_lang['sweep_unused_files'] = 'unused files';
$_lang['sweep_used_files'] = 'used files';
$_lang['sweep_total_size'] = 'Total size';

$_lang['sweep_selected'] = 'Selected';

$_lang['sweep_directory_create'] = 'Create directory';
$_lang['sweep_directory_update'] = 'Update directory';
$_lang['sweep_directory_enable'] = 'Enable directory';
$_lang['sweep_directories_enable'] = 'Enable directories';
$_lang['sweep_directory_disable'] = 'Disable directory';
$_lang['sweep_directories_disable'] = 'Disable directories';
$_lang['sweep_directory_remove'] = 'Remove directory';
$_lang['sweep_directories_remove'] = 'Remove directories';
$_lang['sweep_directory_remove_confirm'] = 'Are you sure you want to remove this directory?';
$_lang['sweep_directories_remove_confirm'] = 'Are you sure you want to remove this directories?';

$_lang['sweep_directory_err_name'] = 'You must specify the name of directory.';
$_lang['sweep_directory_err_ae'] = 'A directory already exists with that name.';
$_lang['sweep_directory_err_nf'] = 'Directory not found.';
$_lang['sweep_directory_err_ns'] = 'Directory not specified.';
$_lang['sweep_directory_err_remove'] = 'An error occurred while trying to remove the directory.';
$_lang['sweep_directory_err_save'] = 'An error occurred while trying to save the directory.';
