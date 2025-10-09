@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Calendrier visuel -->
                    <div class="calendar-container">
                        <div class="calendar-header text-center mb-3">
                            <h4 class="calendar-title">CALENDRIER DES COLLECTES</h4>
                            
                            <div class="month-navigation d-flex align-items-center justify-content-center">
                                <button class="btn btn-link p-0 me-4" id="prevMonth">
                                    <i class="fas fa-chevron-left fa-lg"></i>
                                </button>
                                <h5 class="month-year mb-0 mx-4" id="currentMonth">MAI 2024</h5>
                                <button class="btn btn-link p-0 ms-4" id="nextMonth">
                                    <i class="fas fa-chevron-right fa-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="calendar-grid">
                            <!-- En-têtes des jours -->
                            <div class="calendar-weekdays">
                                <div class="weekday">LU</div>
                                <div class="weekday">MA</div>
                                <div class="weekday">ME</div>
                                <div class="weekday">JE</div>
                                <div class="weekday">VE</div>
                                <div class="weekday">SA</div>
                                <div class="weekday">DI</div>
                            </div>

                            <!-- Grille des dates -->
                            <div class="calendar-dates" id="calendarDates">
                                <!-- Les dates seront générées par JavaScript -->
                            </div>
                        </div>

                        <!-- Légende -->
                        <div class="calendar-legend mt-3 text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="legend-item d-flex align-items-center me-4">
                                    <div class="legend-color bg-primary rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                    <span class="small">Date de collecte</span>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
    </div>
</div>

<style>
.calendar-container {
    max-width: 500px;
    margin: 0 auto;
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.calendar-title {
    font-weight: bold;
    color: #333;
    font-size: 1rem;
    margin-bottom: 15px;
}

.month-year {
    font-weight: bold;
    color: #333;
    font-size: 1rem;
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    margin-bottom: 5px;
}

.weekday {
    text-align: center;
    padding: 6px;
    font-weight: bold;
    color: #333;
    background: #f8f9fa;
    font-size: 0.8rem;
}

.calendar-dates {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 1px solid #e9ecef;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.8rem;
    color: #333;
    min-height: 35px;
}

.calendar-day:hover {
    background: #f8f9fa;
}

.calendar-day.collection-day {
    background: #007bff;
    color: white;
    font-weight: bold;
    border-color: #007bff;
}

.calendar-day.other-month {
    color: #ccc;
    background: #f8f9fa;
}

.calendar-day.today {
    border: 2px solid #28a745;
    font-weight: bold;
}

.calendar-legend {
    margin-top: 10px;
}

.legend-color {
    width: 10px;
    height: 10px;
}

@media (max-width: 768px) {
    .calendar-container {
        padding: 10px;
        max-width: 400px;
    }
    
    .calendar-title {
        font-size: 0.9rem;
        margin-bottom: 10px;
    }
    
    .month-year {
        font-size: 0.9rem;
    }
    
    .weekday {
        padding: 4px;
        font-size: 0.7rem;
    }
    
    .calendar-day {
        font-size: 0.7rem;
        min-height: 30px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarDates = document.getElementById('calendarDates');
    const currentMonthElement = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    let currentDate = new Date();
    currentDate.setDate(1); // Premier jour du mois

    // Dates de collecte depuis le serveur
    const collectionDates = @json($collectionDates ?? []);

    function updateCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        // Mettre à jour l'affichage du mois/année
        const monthNames = [
            'JANVIER', 'FÉVRIER', 'MARS', 'AVRIL', 'MAI', 'JUIN',
            'JUILLET', 'AOÛT', 'SEPTEMBRE', 'OCTOBRE', 'NOVEMBRE', 'DÉCEMBRE'
        ];
        currentMonthElement.textContent = `${monthNames[month]} ${year}`;

        // Mettre à jour l'affichage

        // Générer les dates du calendrier
        calendarDates.innerHTML = '';
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const firstDayWeek = firstDay.getDay() === 0 ? 7 : firstDay.getDay(); // Convertir dimanche (0) en 7
        const daysInMonth = lastDay.getDate();

        // Ajouter les jours du mois précédent
        const prevMonth = new Date(year, month, 0);
        const daysInPrevMonth = prevMonth.getDate();
        for (let i = firstDayWeek - 2; i >= 0; i--) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day other-month';
            dayElement.textContent = daysInPrevMonth - i;
            calendarDates.appendChild(dayElement);
        }

        // Ajouter les jours du mois actuel
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;

            // Vérifier si c'est une date de collecte
            if (collectionDates.includes(day)) {
                dayElement.classList.add('collection-day');
            }

            // Vérifier si c'est aujourd'hui
            const today = new Date();
            if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                dayElement.classList.add('today');
            }

            calendarDates.appendChild(dayElement);
        }

        // Ajouter les jours du mois suivant pour compléter la grille
        const remainingCells = 42 - (firstDayWeek - 1 + daysInMonth); // 6 semaines × 7 jours = 42 cellules
        for (let day = 1; day <= remainingCells; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day other-month';
            dayElement.textContent = day;
            calendarDates.appendChild(dayElement);
        }
    }

    // Fonction pour charger les dates de collecte pour un mois donné
    async function loadCollectionDates(year, month) {
        try {
            const response = await fetch(`{{ route('citoyen.calendrier.index') }}?month=${year}-${String(month + 1).padStart(2, '0')}&ajax=1`);
            const data = await response.json();
            return data.collectionDates || [];
        } catch (error) {
            console.error('Erreur lors du chargement des dates:', error);
            return [];
        }
    }

    // Navigation par mois
    prevMonthBtn.addEventListener('click', async function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        const newDates = await loadCollectionDates(currentDate.getFullYear(), currentDate.getMonth());
        collectionDates.length = 0;
        collectionDates.push(...newDates);
        updateCalendar();
    });

    nextMonthBtn.addEventListener('click', async function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        const newDates = await loadCollectionDates(currentDate.getFullYear(), currentDate.getMonth());
        collectionDates.length = 0;
        collectionDates.push(...newDates);
        updateCalendar();
    });

    // Navigation par flèches uniquement

    // Initialiser le calendrier
    updateCalendar();
});
</script>
@endsection
