document.addEventListener('DOMContentLoaded', function() {
    const departemenSelect = document.getElementById('departemen');
    const supervisorInput = document.getElementById('supervisor');

    // Function to update supervisor
    function updateSupervisor(departemenId) {
        if (!departemenId) {
            supervisorInput.value = '';
            return;
        }

        fetch(`/get-supervisor/${departemenId}`)
            .then(response => response.json())
            .then(data => {
                supervisorInput.value = data.supervisor || '';
            })
            .catch(error => {
                console.error('Error:', error);
                supervisorInput.value = '';
            });
    }

    // Event listener for departemen change
    if (departemenSelect && supervisorInput) {
        // Initial load if value exists
        if (departemenSelect.value) {
            updateSupervisor(departemenSelect.value);
        }

        // On change event
        departemenSelect.addEventListener('change', function() {
            updateSupervisor(this.value);
        });
    }
});