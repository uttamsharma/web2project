<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$AppUI->savePlace();

// retrieve any state parameters
if (isset($_REQUEST['project_id'])) {
	$AppUI->setState('LinkIdxProject', w2PgetParam($_REQUEST, 'project_id', null));
}

$project_id = $AppUI->getState('LinkIdxProject') !== null ? $AppUI->getState('LinkIdxProject') : 0;

if (w2PgetParam($_GET, 'tab', -1) != -1) {
	$AppUI->setState('LinkIdxTab', w2PgetParam($_GET, 'tab'));
}
$tab = $AppUI->getState('LinkIdxTab') !== null ? $AppUI->getState('LinkIdxTab') : 0;
$active = intval(!$AppUI->getState('LinkIdxTab'));

require_once ($AppUI->getModuleClass('projects'));

// get the list of visible companies
$extra = array('from' => 'links', 'where' => 'projects.project_id = link_project');

$project = new CProject();
$projects = $project->getAllowedRecords($AppUI->user_id, 'projects.project_id,project_name', 'project_name', null, $extra, 'projects');
$projects = arrayMerge(array('0' => $AppUI->_('All', UI_OUTPUT_JS)), $projects);

// setup the title block
$titleBlock = new CTitleBlock('Links', 'folder5.png', $m, "$m.$a");
$titleBlock->addCell($AppUI->_('Search') . ':');
$titleBlock->addCell('<input type="text" class="text" SIZE="10" name="search" onChange="document.searchfilter.submit();" value=' . "'$search'" . 'title="' . $AppUI->_('Search in name and description fields', UI_OUTPUT_JS) . '"/>', '', '<form action="?m=links" method="post" id="searchfilter">', '</form>');
$titleBlock->addCell($AppUI->_('Filter') . ':');
$titleBlock->addCell(arraySelect($projects, 'project_id', 'onChange="document.pickProject.submit()" size="1" class="text"', $project_id), '', '<form name="pickProject" action="?m=links" method="post">', '</form>');
if ($canEdit) {
	$titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('new link') . '">', '', '<form action="?m=links&a=addedit" method="post">', '</form>');
}
$titleBlock->show();

$link_types = w2PgetSysVal('LinkType');
if ($tab != -1) {
	array_unshift($link_types, 'All Links');
}
array_map(array($AppUI, '_'), $link_types);

$tabBox = new CTabBox('?m=links', W2P_BASE_DIR . '/modules/links/', $tab);

$i = 0;

foreach ($link_types as $link_type) {
	$tabBox->add('index_table', $link_type);
	++$i;
}

$tabBox->show();

?>