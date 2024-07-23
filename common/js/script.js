const fadeIn = document.getElementsByClassName("fade_in");

const backGround = document.getElementsByClassName("back_ground");

const fadeInShow = () => {
    for (let i = 0; i < fadeIn.length; i++) {
        const keyframes = {
            color: ["#999", "#000"],
            opacity: [0, 1],
            translate: ["0 50px", 0],
        };

        const options = {
            duration: 2500,
            easing: "ease-out",
        };

        fadeIn[i].animate(keyframes, options);
    }
};

const backGroundWaver = () => {
    const keyframes = {
        borderRadius: [
            "50% 30% 60% 30% / 30% 40% 80%",
            "30% 70% 50% 40% / 50% 30% 20%",
            "50% 40% 30% 90% / 30% 70% 50%",
            "80% 70% 30% 90% / 40% 90% 30%",
        ],
    };

    const options = {
        duration: 7000,
        direction: "alternate",
        iterations: Infinity,
    };

    backGround[0].animate(keyframes, options);
};

fadeIn.onload = fadeInShow();
backGround.onload = backGroundWaver();
