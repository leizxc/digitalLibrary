 function togglepassword(id, eyeIcon){
            const passwordInput = document.getElementById(id);

            if (passwordInput.type === "password"){
                passwordInput.type = "text";

                eyeIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                        <path d="M2 12s4-7.5 10-7.5 10 7.5 10 7.5-4 7.5-10 7.5S2 12 2 12zm10 3a3 3 0 100-6 3 3 0 000 6z"/>
                        <line x1="2" y1="2" x2="22" y2="22" stroke="currentColor" stroke-width="2"/>
                    </svg>`;
            } else {
                passwordInput.type = "password";

                eyeIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                        <path d="M12 4.5c-7 0-11 7.5-11 7.5s4 7.5 11 7.5 11-7.5 11-7.5-4-7.5-11-7.5zm0 12c-2.5 0-4.5-2-4.5-4.5S9.5 7.5 12 7.5s4.5 2 4.5 4.5-2 4.5-4.5 4.5z"/>
                    </svg>`;
            }
        }

        function showForm(formType){
            document.getElementById("login-form").style.display = (formType === "login") ? "block" : "none";
            document.getElementById("signup-form").style.display = (formType === "signup") ? "block" : "none";
        }