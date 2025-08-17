$(document).ready(function () {
    // Hiển thị modal


    function showModal() {
        const locationModal = document.getElementById("locationModal");
        locationModal.classList.add("active");
    }
    // Hiển thị vị trí với hiệu ứng mượt
    function showUserLocation(message) {
        const locationDisplay = document.getElementById("userLocationDisplay");

        // Cập nhật nội dung
        locationDisplay.textContent = message;

        // Thêm lớp hiển thị
        locationDisplay.classList.add("show");

        // Ẩn sau 5 giây
        setTimeout(() => {
            locationDisplay.classList.remove("show");
        }, 15000); // 5 giây
    }
    // Ẩn modal
    function hideModal() {
        const locationModal = document.getElementById("locationModal");
        locationModal.classList.remove("active");
    }
    // Kiểm tra vị trí và hiển thị
    async function displayUserLocation() {
        const storedLocation = localStorage.getItem("userLocation");

        let location = [];

        if (storedLocation) location = JSON.parse(storedLocation);
        else location = await fetchLocationFromIpInfo();

        if (location) {
            const message = `Welcome customers from ${location.city}`;
            showUserLocation(message);
            localStorage.setItem("userLocation", JSON.stringify(location)); // Lưu vào localStorage
        } else {
            showUserLocation("Unable to determine your location.");
        }
    }

    // Kiểm tra và hiển thị khi trang được tải
    // Lấy vị trí từ ipinfo.io
    async function fetchLocationFromIpInfo() {
        const apiUrl = "https://ipinfo.io/json?token=906131401b84ff"; // Sử dụng token của bạn

        try {
            const response = await fetch(apiUrl);

            if (!response.ok) {
                throw new Error(`HTTP lỗi: ${response.status}`);
            }

            const data = await response.json();
            console.log("Dữ liệu từ ipinfo.io:", data);

            // Kết hợp thông tin cần thiết
            return {
                city: data.city || "Không xác định", // Thành phố hoặc quận/huyện
                region: data.region || "Không xác định", // Tỉnh/thành phố
                country: data.country || "Không xác định", // Quốc gia
            };
        } catch (error) {
            console.error("Lỗi từ ipinfo.io:", error.message);
            return {
                city: "Không xác định",
                region: "Không xác định",
                country: "Không xác định",
            };
        }
    }



    // Kiểm tra xem vị trí đã được lưu trong localStorage chưa
    function checkLocation() {
        const storedLocation = localStorage.getItem("userLocation");

        if (storedLocation) {
            const location = JSON.parse(storedLocation);
            displayUserLocation(location);
        } else {
            showModal(); // Hiển thị modal yêu cầu định vị
        }
    }

    // Sự kiện khi người dùng nhấn "Bật Định Vị"
    document.getElementById("grantPermission").addEventListener("click", async () => {
        hideModal();
        const location = await fetchLocationFromIpInfo();
        displayUserLocation(location);

        // Lưu thông tin vị trí vào localStorage để không hiển thị lại
        localStorage.setItem("userLocation", JSON.stringify(location));
    });

    // const storedLocation = localStorage.getItem("userLocation");

    // if (storedLocation) {
    //     const location = JSON.parse(storedLocation);
    //     const message = `Welcome customers from ${location.city}`;
    //     showUserLocation(message);
    // } else {
    //     displayUserLocation(); // Lấy vị trí mới nếu chưa có
    // }

    // Kiểm tra vị trí khi tải trang
    checkLocation();
});

document.addEventListener("DOMContentLoaded", function () {
    const lazyImages = document.querySelectorAll("img[data-src]");
    const banner = document.querySelector('.top-carousel'); // Phần chứa các ảnh
    let currentIndex = 0; // Chỉ số của ảnh hiện tại
    const totalImages = lazyImages.length; // Tổng số ảnh trong banner

    // Lazy loading cho hình ảnh
    const lazyLoad = (image) => {
        const src = image.getAttribute("data-src");
        if (!src) return;
        image.src = src;
        image.removeAttribute("data-src");
    };

    const observer = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    lazyLoad(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            rootMargin: "0px 0px 50px 0px",
            threshold: 0.01,
        }
    );

    lazyImages.forEach((image) => observer.observe(image));
    //const max_index = totalImages / 10;
    // Tự động lướt ảnh trong banner sau 3 giây
    // const autoSlide = () => {
    //     currentIndex = (currentIndex + 1) % Math.floor((totalImages / 9 + 1)); // Chuyển sang ảnh tiếp theo
    //     banner.style.transform = `translateX(-${currentIndex * 100}%)`; // Lướt ảnh
    // };

    // Thiết lập interval để tự động lướt
    //setInterval(autoSlide, 3000);
});


let online = parseInt(localStorage.getItem("online")) || 204;
let vipMembers = parseInt(localStorage.getItem("vipMembers")) || 4197;
let visitCount = parseInt(localStorage.getItem("visitCount")) || 218483;
function updateSydneyTime() { 
    const now = new Date(); 
    const sydneyTime = new Date(now.toLocaleString("en-US", { timeZone: "Australia/Sydney" }));
    const hours = sydneyTime.getHours().toString().padStart(2, "0");
    const minutes = sydneyTime.getMinutes().toString().padStart(2, "0");
    const seconds = sydneyTime.getSeconds().toString().padStart(2, "0");
    const timeDifference = ((sydneyTime - now) / (1000 * 60 * 60)).toFixed(1);
    document.getElementById("australiaTime").innerText = `${hours}:${minutes}:${seconds}`;
    //document.getElementById("timeDifference").innerText = `${timeDifference}giờ`;
}
function updateOnline() { const change = Math.floor(Math.random() * 11) + 10; const increaseOrDecrease = Math.random() < 0.5 ? -1 : 1; online = Math.max(100, Math.min(250, online + change * increaseOrDecrease)); document.getElementById("online").innerText = online.toLocaleString(); localStorage.setItem("online", online); }
function updateVipMembers() { vipMembers++; document.getElementById("vipMembers").innerText = vipMembers.toLocaleString(); localStorage.setItem("vipMembers", vipMembers); }
function updateVisitCount() { const increment = Math.floor(Math.random() * 50) + 1; visitCount += increment; document.getElementById("visitCount").innerText = visitCount.toLocaleString(); localStorage.setItem("visitCount", visitCount); }
document.addEventListener("DOMContentLoaded", () => { document.getElementById("online").innerText = online.toLocaleString(); document.getElementById("vipMembers").innerText = vipMembers.toLocaleString(); document.getElementById("visitCount").innerText = visitCount.toLocaleString(); setInterval(updateSydneyTime, 1000); setInterval(updateOnline, 5000); setInterval(updateVipMembers, 600000); setInterval(updateVisitCount, 10000); });


document.addEventListener("DOMContentLoaded", () => {
  const snowContainer = document.querySelector(".snowflakes");
  const maxSnowflakes = 10; // Giảm số lượng hạt tuyết xuống 10
  let currentSnowflakes = 0;

  function createSnowflake() {
    if (currentSnowflakes >= maxSnowflakes) return;

    const snowflake = document.createElement("div");
    snowflake.classList.add("snowflake");
    snowflake.innerHTML = "❄";
    snowflake.style.left = Math.random() * 100 + "vw";
    snowflake.style.animationDuration = Math.random() * 5 + 8 + "s"; // Thời gian rơi từ 8s đến 13s
    snowflake.style.fontSize = Math.random() * 0.4 + 0.6 + "em"; // Kích thước từ 0.6em đến 1em
    snowContainer.appendChild(snowflake);
    currentSnowflakes++;

    // Xóa hạt tuyết khi kết thúc animation và tạo lại sau một khoảng thời gian trễ
    snowflake.addEventListener("animationend", () => {
      snowflake.remove();
      currentSnowflakes--;
      setTimeout(createSnowflake, 2000); // Trễ 2 giây trước khi tạo lại hạt mới
    });
  }

  // Tạo hạt tuyết ban đầu
  for (let i = 0; i < maxSnowflakes; i++) {
    setTimeout(createSnowflake, i * 1000); // Tạo cách nhau 1 giây
  }
});