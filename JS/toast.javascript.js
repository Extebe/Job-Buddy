//Permet d'afficher une notification avec un message d'alerte de bootstrapt
document.addEventListener('DOMContentLoaded', () => {
                    const toastEl = document.getElementById('myToast');
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });

