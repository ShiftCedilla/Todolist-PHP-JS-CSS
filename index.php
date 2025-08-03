<?php
// Démarrage de la session pour stocker les données entre les requêtes
session_start();

// Initialiser les tâches dans la session si elles n'existent pas
// Cela évite les erreurs lors de la première visite
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

/**
 * Fonction pour obtenir les tâches filtrées par priorité
 * @param string $priority - La priorité à filtrer (urgent, moins_urgent, osef)
 * @return array - Tableau des tâches non complétées de cette priorité
 */
function getTasksByPriority($priority) {
    global $_SESSION;
    $tasks = [];
    
    // Parcourir toutes les tâches et filtrer par priorité
    foreach ($_SESSION['tasks'] as $task) {
        // Ne récupérer que les tâches non complétées de la priorité demandée
        if ($task['priority'] === $priority && !$task['completed']) {
            $tasks[] = $task;
        }
    }
    
    // Trier par date de création (plus récent en premier)
    usort($tasks, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    return $tasks;
}

/**
 * Fonction pour afficher une tâche individuelle
 * @param array $task - La tâche à afficher
 */
function displayTask($task) {
    // Déterminer si la case à cocher doit être cochée
    $checked = $task['completed'] ? 'checked' : '';
    
    // Ajouter la classe CSS pour les tâches complétées
    $completedClass = $task['completed'] ? 'completed' : '';
    
    // Générer le HTML pour une tâche
    echo '<div class="task-item ' . $completedClass . '" data-id="' . $task['id'] . '">';
    echo '<input type="checkbox" class="task-checkbox" ' . $checked . ' onchange="toggleTask(' . $task['id'] . ')">';
    echo '<span class="task-description">' . htmlspecialchars($task['description']) . '</span>';
    echo '<span class="task-date">' . date('d/m/Y H:i', strtotime($task['created_at'])) . '</span>';
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList Collaborative</title>
    <!-- Lien vers la feuille de style CSS -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- Conteneur principal de l'application -->
    <div class="container">
        <!-- En-tête avec titre et navigation -->
        <header>
            <h1>TodoList W.A.D</h1>
            <!-- Barre de navigation avec boutons d'action -->
            <nav class="navBar">
                <!-- Bouton texte pour supprimer les tâches complétées -->
                <button class="delete" onclick="clearCompleted()">Eléments supprimés</button>
                <!-- Bouton avec icône pour supprimer les tâches complétées -->
                <button class="imgDelete" onclick="clearCompleted()"><img class="poubelle" src="assets/img/images.png" alt="poubelle"></button>
            </nav>
        </header>

        <!-- Section pour ajouter de nouvelles tâches -->
        <div class="add-task-form">
            <h2>Ajouter une tâche</h2>
            <!-- Formulaire d'ajout de tâche -->
            <form action="traitement.php" method="POST">
                <!-- Champ de saisie pour la description de la tâche -->
                <input type="text" name="task_description" placeholder="Description..." required>
                <!-- Menu déroulant pour sélectionner la priorité -->
                <select name="priority" required>
                    <option value="">Priorité</option>
                    <option value="urgent">Urgent</option>
                    <option value="moins_urgent">Moins urgent</option>
                    <option value="osef">Osef</option>
                </select>
                <!-- Bouton pour soumettre le formulaire -->
                <button type="submit" name="action" value="add">Ajouter</button>
            </form>
        </div>

        <!-- Conteneur des 3 colonnes de priorités -->
        <div class="columns-container">
            <!-- Colonne pour les tâches urgentes -->
            <div class="column urgent-column">
                <h3>Urgent</h3>
                <!-- Liste des tâches urgentes -->
                <div class="tasks-list" id="urgent-tasks">
                    <?php
                    // Récupérer et afficher les tâches urgentes
                    $urgentTasks = getTasksByPriority('urgent');
                    foreach ($urgentTasks as $task) {
                        displayTask($task);
                    }
                    ?>
                </div>
            </div>

            <!-- Colonne pour les tâches moins urgentes -->
            <div class="column moins-urgent-column">
                <h3>Moins urgent</h3>
                <!-- Liste des tâches moins urgentes -->
                <div class="tasks-list" id="moins-urgent-tasks">
                    <?php
                    // Récupérer et afficher les tâches moins urgentes
                    $moinsUrgentTasks = getTasksByPriority('moins_urgent');
                    foreach ($moinsUrgentTasks as $task) {
                        displayTask($task);
                    }
                    ?>
                </div>
            </div>

            <!-- Colonne pour les tâches "osef" (peu importantes) -->
            <div class="column osef-column">
                <h3>Osef</h3>
                <!-- Liste des tâches "osef" -->
                <div class="tasks-list" id="osef-tasks">
                    <?php
                    // Récupérer et afficher les tâches "osef"
                    $osefTasks = getTasksByPriority('osef');
                    foreach ($osefTasks as $task) {
                        displayTask($task);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Inclusion du fichier JavaScript pour les interactions -->
    <script src="assets/script.js"></script>
</body>
</html> 