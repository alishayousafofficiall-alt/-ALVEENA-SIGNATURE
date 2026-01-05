<?php
require_once 'db/conn.php';
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$q = trim($q);

if ($q === '') {
    echo json_encode([]);
    exit;
}

// Step 1: Find categories starting with $q
$stmt = $pdo->prepare("SELECT id, name, image FROM categories WHERE name LIKE ? LIMIT 10");
$stmt->execute(["$q%"]);
$matched = $stmt->fetchAll(PDO::FETCH_ASSOC);

$allCategories = [];
foreach ($matched as $cat) {
    $allCategories[$cat['id']] = $cat;
}

// Step 2: Find child categories of matched categories
if (!empty($allCategories)) {
    $parentIds = array_keys($allCategories);
    $placeholders = implode(',', array_fill(0, count($parentIds), '?'));
    
    $childStmt = $pdo->prepare("SELECT id, name, image FROM categories WHERE parent_id IN ($placeholders)");
    $childStmt->execute($parentIds);
    $children = $childStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($children as $child) {
        $allCategories[$child['id']] = $child;
    }
    
    // Step 3: Find grandchildren categories (children of children)
    if (!empty($children)) {
        $childIds = array_column($children, 'id');
        $placeholders2 = implode(',', array_fill(0, count($childIds), '?'));
        
        $grandchildStmt = $pdo->prepare("SELECT id, name, image FROM categories WHERE parent_id IN ($placeholders2)");
        $grandchildStmt->execute($childIds);
        $grandchildren = $grandchildStmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($grandchildren as $grandchild) {
            $allCategories[$grandchild['id']] = $grandchild;
        }
    }
}

// Return all unique categories, sorted by name
$results = array_values($allCategories);
usort($results, fn($a, $b) => strcasecmp($a['name'], $b['name']));

echo json_encode($results);