// Fonction pour basculer l'état d'une tâche (complétée/non complétée)
function toggleTask(taskId) {
    fetch('traitement.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=toggle&task_id=' + taskId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Si la tâche est complétée, la supprimer après 3 secondes
            if (data.completed) {
                setTimeout(() => {
                    deleteTask(taskId);
                }, 3000);
            }
            // Recharger la page pour afficher les changements
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Fonction pour supprimer une tâche
function deleteTask(taskId) {
    fetch('traitement.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=delete&task_id=' + taskId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Supprimer l'élément du DOM
            const taskElement = document.querySelector(`[data-id="${taskId}"]`);
            if (taskElement) {
                taskElement.remove();
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Fonction pour supprimer toutes les tâches complétées
function clearCompleted() {
    fetch('traitement.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=clear_completed'
    })
    .then(() => {
        // Recharger la page pour afficher les changements
        location.reload();
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Fonction pour rafraîchir les tâches
function refreshTasks() {
    location.reload();
}

// Auto-refresh toutes les 30 secondes
setInterval(refreshTasks, 30000); 