<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

function dashboard_read($userId) {

    $result = db_select('SELECT category.id, category.title, category.parentId, (0) AS shared FROM category LEFT OUTER JOIN category as child ON category.id=child.parentId WHERE category.userId='.$userId);

    $categories = [];

    while($row = $result->fetch_assoc()) {

        array_push($categories, $row);
    }

    // search for categories that user is the owner or any task
    // $categories = db_select('SELECT category.id, category.title, category.parentId, (category.userId != '.$userId.') AS shared FROM category INNER JOIN task ON category.id=task.categoryId WHERE category.userId != '.$userId.' AND task.assignedTo='.$userId);

    $result = db_select('SELECT category.id, category.title, category.parentId, (1) AS shared FROM category INNER JOIN task ON task.categoryId=category.id WHERE category.userId!='.$userId.' AND task.assignedTo='.$userId);

    while($row = $result->fetch_assoc()){

        array_push($categories, $row);
    }

    $output = [];

    $parentCategories = [];

    foreach($categories as $category) {

        if(!isset($category['parentId'])) {

            // push category to array
            array_push($parentCategories, $category);
        }
    }

    foreach($parentCategories as $parentCategory) {

        $parentCategory['subCategories'] = [];

        foreach($categories as $childCategory) {

            if($childCategory['parentId'] == $parentCategory['id']) {

                array_push($parentCategory['subCategories'], $childCategory);
            }
        }

        if(count($parentCategory['subCategories']) == 0) {



            unset($parentCategory['subCategories']);
        }

        array_push($output, $parentCategory);
    }

    return $output;
}

function load_sub_categories($parentCategories) {

}

function load_tasks($userId) {

}