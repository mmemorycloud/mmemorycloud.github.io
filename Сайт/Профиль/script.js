// Функция для загрузки данных о пользователе и его фотографиях
function loadUserData() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                document.getElementById("username").textContent = response.login;
                document.getElementById("useremail").textContent = response.email;

                // Загрузка фотографий пользователя
                loadUserPhotos(response.photos);

                // Обновляем аватар пользователя, если он есть
                if (response.avatar) {
                    document.getElementById("userAvatar").src = response.avatar;
                }
            } else {
                console.error("Ошибка загрузки данных о пользователе.");
            }
        }
    };
    xhr.open("GET", "profile.php", true);
    xhr.send();
}

// Обработчик события отправки формы загрузки аватарки
document.getElementById("uploadAvatarForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Предотвращаем отправку формы по умолчанию
    var formData = new FormData(this); // Создаем объект FormData для отправки данных формы

    // Отправляем запрос на сервер
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                alert("Аватар успешно загружен и сохранен.");
                loadUserData(); // Обновляем данные о пользователе после успешной загрузки аватарки
            } else {
                alert("Ошибка при загрузке аватарки.");
            }
        }
    };
    xhr.open("POST", "change_avatar.php", true);
    xhr.send(formData); // Отправляем данные формы на сервер
});

// Обработчик события нажатия на аватар пользователя
document.getElementById("userAvatar").addEventListener("click", function() {
    var avatarOptions = document.getElementById("avatar-options");
    avatarOptions.classList.toggle("show");
});

// Функция для загрузки фотографий пользователя
function loadUserPhotos(photos) {
    var photosContainer = document.getElementById("gallery");
    photosContainer.innerHTML = ""; // Очищаем контейнер с фотографиями перед загрузкой новых

    photos.forEach(function(photo) {
        var img = document.createElement("img");
        img.src = photo;
        photosContainer.appendChild(img);
    });
}

// Загружаем данные о пользователе при загрузке страницы
window.onload = loadUserData;
