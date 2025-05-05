let currentQuestion = 0;
let score = userData.score;
const questions = syllabus.questions;

const scoreDiv = document.getElementById("score");
const questionDiv = document.getElementById("question");
const optionsDiv = document.getElementById("options");
const feedbackDiv = document.getElementById("feedback");
const nextBtn = document.getElementById("next");

function showQuestion() {
    feedbackDiv.textContent = "";
    optionsDiv.innerHTML = "";
    nextBtn.style.display = "none";

    if (currentQuestion >= questions.length) {
        questionDiv.textContent = "🎉 Quiz terminé ! Score final : " + score;
        nextBtn.style.display = "none";

        // Envoi du score à PHP
        fetch("update_user.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                username: userData.username,
                score: score
            })
        })
        .then(res => res.ok ? console.log("Score mis à jour.") : console.error("Erreur serveur"))
        .catch(err => console.error("Erreur réseau :", err));

        return;
    }

    const q = questions[currentQuestion];
    questionDiv.textContent = q.question;

    q.options.forEach((opt, index) => {
        const btn = document.createElement("button");
        btn.textContent = opt;
        btn.classList.add("option-btn");
        btn.onclick = () => checkAnswer(index, q.answer);
        optionsDiv.appendChild(btn);
    });
}

function checkAnswer(selectedIndex, correctIndex) {
    const allButtons = document.querySelectorAll(".option-btn");
    allButtons.forEach(btn => btn.disabled = true);

    if (selectedIndex === correctIndex) {
        feedbackDiv.textContent = "✅ Bonne réponse !";
        feedbackDiv.style.color = "green";
        score++;
        scoreDiv.textContent = "Score : " + score;
    } else {
        feedbackDiv.textContent = "❌ Mauvaise réponse. Bonne réponse : " + questions[currentQuestion].options[correctIndex];
        feedbackDiv.style.color = "red";
    }

    nextBtn.style.display = "inline-block";
}

nextBtn.addEventListener("click", () => {
    currentQuestion++;
    showQuestion();
});

showQuestion();
