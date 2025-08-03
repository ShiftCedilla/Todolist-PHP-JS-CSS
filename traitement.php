<?php
session_start();

// Configuration des cookies
$cookie_name = "todolist_user";
$cookie_value = "user_" . uniqid();
$cookie_expiry = time() + (86400 * 30); // 30 jours

// Définir le cookie si il n'existe pas
if (!isset($_COOKIE[$cookie_name])) {
    setcookie($cookie_name, $cookie_value, $cookie_expiry, "/");
}

// Initialiser les tâches dans la session si elles n'existent pas
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            // Ajouter une nouvelle tâche
            if (isset($_POST['task_description']) && isset($_POST['priority'])) {
                $newTask = [
                    'id' => uniqid(),
                    'description' => trim($_POST['task_description']),
                    'priority' => $_POST['priority'],
                    'completed' => false,
                    'created_at' => date('Y-m-d H:i:s'),
                    'user_id' => $_COOKIE[$cookie_name] ?? $cookie_value
                ];
                
                $_SESSION['tasks'][] = $newTask;
                
                // Rediriger vers la page principale
                header('Location: index.php');
                exit();
            }
            break;
            
        case 'toggle':
            // Basculer l'état d'une tâche (complétée/non complétée)
            if (isset($_POST['task_id'])) {
                $taskId = $_POST['task_id'];
                
                foreach ($_SESSION['tasks'] as &$task) {
                    if ($task['id'] === $taskId) {
                        $task['completed'] = !$task['completed'];
                        break;
                    }
                }
                
                // Si la tâche est complétée, la supprimer après 3 secondes
                if ($task['completed']) {
                    // Stocker l'ID de la tâche à supprimer dans un cookie temporaire
                    setcookie("task_to_delete", $taskId, time() + 5, "/");
                }
                
                // Réponse JSON pour AJAX
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'completed' => $task['completed']]);
                exit();
            }
            break;
            
        case 'delete':
            // Supprimer une tâche
            if (isset($_POST['task_id'])) {
                $taskId = $_POST['task_id'];
                
                foreach ($_SESSION['tasks'] as $key => $task) {
                    if ($task['id'] === $taskId) {
                        unset($_SESSION['tasks'][$key]);
                        break;
                    }
                }
                
                // Réindexer le tableau
                $_SESSION['tasks'] = array_values($_SESSION['tasks']);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit();
            }
            break;
            
        case 'clear_completed':
            // Supprimer toutes les tâches complétées
            foreach ($_SESSION['tasks'] as $key => $task) {
                if ($task['completed']) {
                    unset($_SESSION['tasks'][$key]);
                }
            }
            
            // Réindexer le tableau
            $_SESSION['tasks'] = array_values($_SESSION['tasks']);
            
            header('Location: index.php');
            exit();
            break;
    }
}

// Si aucune action valide, rediriger vers la page principale
header('Location: index.php');
exit();
?> 