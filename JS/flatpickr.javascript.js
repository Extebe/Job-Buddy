//pour la page ajouterAnnonce
document.addEventListener("DOMContentLoaded",()=>{
    //configuration en français
    const configFr={
        enableTime: true,
        time_24hr: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today",
        locale: "fr"
    };

    const dateDebut = flatpickr("#dateDebut", {
        ...configFr,
        onChange: (selectedDate,dateStr)=>{
            dateFin.set("minDate",dateStr);
        }
    });

    const dateFin = flatpickr("#dateFin",configFr);
});

//pour la page modifierCompte
document.addEventListener("DOMContentLoaded",()=>{
    //configuration en français
    const configFr={
        enableTime: true,
        time_24hr: true,
        dateFormat: "Y-m-d",
        locale: "fr"
    };

    const dateNaiss = flatpickr("#dateNaiss",{configFr});
});

