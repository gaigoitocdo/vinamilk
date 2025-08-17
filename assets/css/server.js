// webhook.js
const express = require('express');
const bodyParser = require('body-parser');
const app = express();

// Chuỗi token bạn cấu hình ở Facebook Developer
const VERIFY_TOKEN = "anhyeu_gpt4_token";

// Sử dụng body-parser để đọc JSON trong request POST
app.use(bodyParser.json());

// 1. Xác thực webhook với Facebook (GET /webhook)
app.get('/webhook', (req, res) => {
    const mode = req.query['hub.mode'];
    const token = req.query['hub.verify_token'];
    const challenge = req.query['hub.challenge'];

    // Kiểm tra xem token và mode có khớp với VERIFY_TOKEN hay không
    if (mode === 'subscribe' && token === VERIFY_TOKEN) {
        console.log("Webhook verified successfully!");
        // Gửi lại challenge để hoàn tất xác thực
        res.status(200).type('text/plain').send(challenge);
    } else {
        // Token sai, trả về 403
        res.sendStatus(403);
    }
});

// 2. Nhận sự kiện Messenger (POST /webhook)
app.post('/webhook', (req, res) => {
    const body = req.body;

    // Facebook gửi object='page' cho các sự kiện Messenger
    if (body.object === 'page') {
        // Có thể có nhiều entry (nếu Facebook batch sự kiện)
        body.entry.forEach(entry => {
            // entry.messaging là mảng chứa các event
            const webhookEvent = entry.messaging[0];
            console.log('Sự kiện Messenger:', webhookEvent);

            // Tại đây bạn xử lý nội dung tin nhắn, postback, v.v.
            // Ví dụ: 
            // if (webhookEvent.message) { ... }
            // if (webhookEvent.postback) { ... }
        });

        // Gửi mã 200 OK cho Facebook biết đã nhận event thành công
        res.status(200).send('EVENT_RECEIVED');
    } else {
        // Nếu event không phải từ page subscription
        res.sendStatus(404);
    }
});

// Lắng nghe cổng 3000
const PORT = 3000;
app.listen(PORT, '0.0.0.0', () => {
    console.log(`Server is running on port ${PORT}`);
});
