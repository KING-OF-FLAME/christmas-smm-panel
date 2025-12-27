// File: assets/js/main.js
document.addEventListener('DOMContentLoaded', () => {
    const spinBtn = document.getElementById('spin-btn');
    const wheelCanvas = document.getElementById('wheel-canvas');
    const refillBadge = document.getElementById('refill-badge');
    
    // --- SETTINGS (Must match PHP order) ---
    const segments = [
        { label: '5 Coins',   color: '#e74c3c' }, // Index 0
        { label: 'Try Again', color: '#ecf0f1' }, // Index 1
        { label: '10 Coins',  color: '#f1c40f' }, // Index 2
        { label: '5 Spins',   color: '#8e44ad' }, // Index 3
        { label: '15 Coins',  color: '#3498db' }, // Index 4
        { label: 'Oops!',     color: '#e67e22' }, // Index 5
        { label: '20 Coins',  color: '#9b59b6' }, // Index 6
        { label: 'Jackpot',   color: '#27ae60' }  // Index 7
    ];

    let currentRotation = 0; 

    // Draw the wheel immediately
    if (wheelCanvas) drawWheel(wheelCanvas, segments);

    // Start Timer if needed
    if(refillBadge) {
        const secondsLeft = parseInt(refillBadge.getAttribute('data-seconds'));
        if(secondsLeft > 0) startBadgeTimer(secondsLeft);
    }

    // Attach Click Event (Only once!)
    if(spinBtn && wheelCanvas) {
        spinBtn.addEventListener('click', startSpin);
    }

    function startSpin() {
        spinBtn.disabled = true;
        spinBtn.innerText = "Spinning...";

        fetch('api/spin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Pass prize text explicitly to avoid "undefined"
                rotateToWinner(data.winning_index, data.prize, data.new_coins, data.spins_left);
            } else {
                alert(data.message);
                if(data.timer_active) window.location.reload();
                spinBtn.disabled = false;
                spinBtn.innerText = "SPIN & WIN";
            }
        })
        .catch(error => {
            console.error(error);
            alert("Network Error");
            spinBtn.disabled = false;
            spinBtn.innerText = "SPIN & WIN";
        });
    }

    function rotateToWinner(index, prizeText, newCoins, spinsLeft) {
        const segmentSize = 360 / segments.length; 
        const centerOffset = segmentSize / 2; 

        // Calculate angle to land on the center of the segment
        const targetAngle = 360 - ((index * segmentSize) + centerOffset);
        
        // Add minimum 5 full spins (1800 deg) + target + random wobble
        const randomVar = Math.floor(Math.random() * 20) - 10; 
        const totalSpin = 1800 + targetAngle + randomVar;

        currentRotation += totalSpin;

        wheelCanvas.style.transition = "transform 4s cubic-bezier(0.2, 0.8, 0.3, 1)";
        wheelCanvas.style.transform = `rotate(${currentRotation}deg)`;

        // Wait 4 seconds for animation to finish
        setTimeout(() => {
            handleWin(prizeText, newCoins, spinsLeft);
        }, 4000);
    }

    function handleWin(prizeText, newCoins, spinsLeft) {
        alert("🎉 You won " + prizeText + "!");
        
        const spinCount = document.getElementById('spin-count');
        if(spinCount) spinCount.innerText = spinsLeft;
        
        // Ensure coin display exists in header
        const coinDisplay = document.querySelector('.fa-coins').nextSibling;
        if(coinDisplay) coinDisplay.nodeValue = " " + newCoins; // Simple text update

        spinBtn.disabled = false;
        spinBtn.innerText = "SPIN AGAIN";

        if(spinsLeft <= 0) {
            setTimeout(() => window.location.reload(), 1000);
        }
    }

    function drawWheel(canvas, segments) {
        const ctx = canvas.getContext('2d');
        const numSegments = segments.length;
        const arcSize = (2 * Math.PI) / numSegments;
        const radius = canvas.width / 2;
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Rotate -90 deg to start drawing from top
        ctx.save();
        ctx.translate(centerX, centerY);
        ctx.rotate(-Math.PI / 2);
        ctx.translate(-centerX, -centerY);

        for (let i = 0; i < numSegments; i++) {
            const angle = i * arcSize;
            
            // Draw Wedge
            ctx.beginPath();
            ctx.fillStyle = segments[i].color;
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, angle, angle + arcSize);
            ctx.lineTo(centerX, centerY);
            ctx.fill();

            // Draw Text
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(angle + arcSize / 2);
            ctx.textAlign = "right";
            ctx.fillStyle = (segments[i].color === '#ecf0f1' || segments[i].color === '#f1c40f') ? "#000" : "#fff";
            ctx.font = "bold 24px Arial";
            ctx.fillText(segments[i].label, radius - 20, 10);
            ctx.restore();
        }
        ctx.restore();
    }

    function startBadgeTimer(seconds) {
        const display = document.getElementById('timer-display');
        const badge = document.getElementById('refill-badge');
        const btn = document.getElementById('spin-btn');
        let timeLeft = seconds;
        
        if(badge) badge.style.display = 'flex'; // Force show

        if(btn) {
            btn.disabled = true;
            btn.innerText = "Refilling...";
            btn.style.background = "#555";
        }
        
        const interval = setInterval(() => {
            if(timeLeft <= 0) {
                clearInterval(interval);
                window.location.reload();
                return;
            }
            
            timeLeft--;
            
            if(display) {
                let m = Math.floor(timeLeft / 60);
                let s = timeLeft % 60;
                display.innerText = `${m}m ${s < 10 ? '0'+s : s}s`;
            }
        }, 1000);
    }
    
    // --- REDEEM LOGIC ---
    const redeemForm = document.getElementById('redeemForm');
    if(redeemForm) {
        redeemForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            const btn = this.querySelector('button');
            const msg = document.getElementById('redeem-msg');
            
            btn.disabled = true;
            btn.innerText = "Applying...";
            msg.innerText = "";

            const formData = new FormData(this);

            fetch('api/redeem.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    msg.style.color = '#4caf50';
                    msg.innerText = "✅ " + data.message;
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    msg.style.color = '#ff6b6b';
                    msg.innerText = "❌ " + data.message;
                    btn.disabled = false;
                    btn.innerText = "Apply";
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = "Retry";
            });
        });
    }
});