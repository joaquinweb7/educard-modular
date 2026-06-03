document.addEventListener('DOMContentLoaded', function() {
    const careerSelect = document.querySelector('.dependent-career');
    const semesterSelect = document.querySelector('.dependent-semester');
    const gestionSelect = document.querySelector('.dependent-gestion');
    const turnoSelect = document.querySelector('.dependent-turno');
    const grupoSelect = document.querySelector('.dependent-grupo');

    // Make sure all elements exist, semesterSelect is now part of the hierarchy
    if (!careerSelect || !semesterSelect || !gestionSelect || !turnoSelect || !grupoSelect) {
        return; 
    }

    const oldSemester = semesterSelect.getAttribute('data-old') || '';
    const oldGestion = gestionSelect.getAttribute('data-old') || '';
    const oldTurno = turnoSelect.getAttribute('data-old') || '';
    const oldGrupo = grupoSelect.getAttribute('data-old') || '';

    function resetSelect(select, defaultText = "— Seleccionar —") {
        select.innerHTML = `<option value="">${defaultText}</option>`;
    }

    function populateSelect(select, data, selectedValue = '') {
        resetSelect(select);
        data.forEach(item => {
            const option = document.createElement('option');
            // Check if item is an object (for semesters it returns {id, name}, for others it returns string)
            if (typeof item === 'object' && item !== null) {
                option.value = item.id;
                option.textContent = item.name;
                if (item.id == selectedValue) {
                    option.selected = true;
                }
            } else {
                option.value = item;
                option.textContent = item;
                if (item === selectedValue) {
                    option.selected = true;
                }
            }
            select.appendChild(option);
        });
    }

    async function fetchOptions(endpoint, params) {
        const query = new URLSearchParams(params).toString();
        try {
            const response = await fetch(`/api/dropdown/${endpoint}?${query}`);
            if (response.ok) {
                return await response.json();
            }
        } catch (error) {
            console.error('Error fetching data:', error);
        }
        return [];
    }

    async function loadSemesters(selectedCareerId, selectedSemester = '') {
        resetSelect(semesterSelect);
        resetSelect(gestionSelect);
        resetSelect(turnoSelect);
        resetSelect(grupoSelect);

        if (!selectedCareerId) return;

        const data = await fetchOptions('semesters', { career_id: selectedCareerId });
        populateSelect(semesterSelect, data, selectedSemester);
        
        if (selectedSemester) {
            loadGestions(selectedCareerId, selectedSemester, oldGestion);
        }
    }

    async function loadGestions(selectedCareerId, selectedSemesterId, selectedGestion = '') {
        resetSelect(gestionSelect);
        resetSelect(turnoSelect);
        resetSelect(grupoSelect);

        if (!selectedCareerId || !selectedSemesterId) return;

        const data = await fetchOptions('gestions', { career_id: selectedCareerId, semester_id: selectedSemesterId });
        populateSelect(gestionSelect, data, selectedGestion);
        
        if (selectedGestion) {
            loadTurnos(selectedCareerId, selectedSemesterId, selectedGestion, oldTurno);
        }
    }

    async function loadTurnos(selectedCareerId, selectedSemesterId, selectedGestion, selectedTurno = '') {
        resetSelect(turnoSelect);
        resetSelect(grupoSelect);

        if (!selectedCareerId || !selectedSemesterId || !selectedGestion) return;

        const data = await fetchOptions('turnos', { career_id: selectedCareerId, semester_id: selectedSemesterId, gestion: selectedGestion });
        populateSelect(turnoSelect, data, selectedTurno);

        if (selectedTurno) {
            loadGrupos(selectedCareerId, selectedSemesterId, selectedGestion, selectedTurno, oldGrupo);
        }
    }

    async function loadGrupos(selectedCareerId, selectedSemesterId, selectedGestion, selectedTurno, selectedGrupo = '') {
        resetSelect(grupoSelect);

        if (!selectedCareerId || !selectedSemesterId || !selectedGestion || !selectedTurno) return;

        const data = await fetchOptions('grupos', { career_id: selectedCareerId, semester_id: selectedSemesterId, gestion: selectedGestion, turno: selectedTurno });
        populateSelect(grupoSelect, data, selectedGrupo);
    }

    // Event Listeners
    careerSelect.addEventListener('change', function() {
        loadSemesters(this.value);
    });

    semesterSelect.addEventListener('change', function() {
        loadGestions(careerSelect.value, this.value);
    });

    gestionSelect.addEventListener('change', function() {
        loadTurnos(careerSelect.value, semesterSelect.value, this.value);
    });

    turnoSelect.addEventListener('change', function() {
        loadGrupos(careerSelect.value, semesterSelect.value, gestionSelect.value, this.value);
    });

    // Carga inicial
    if (careerSelect.value) {
        loadSemesters(careerSelect.value, oldSemester);
    }
});
