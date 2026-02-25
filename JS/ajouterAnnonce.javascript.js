document.addEventListener("DOMContentLoaded",()=>{
    //configuration en français
    const configFr={
        enableTime: true,
        time_24hr: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today",
        // defaultDate: "today",
        // wrap: true,
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