const pages = document.querySelectorAll('.page');
function handleRouting() {
    const hash = window.location.hash || '#home';
    pages.forEach(page => {
      if ('#' + page.id === hash) {
        page.classList.add('active');
        page.classList.remove('hidden');
      } else {
        page.classList.remove('active');
        setTimeout(() => page.classList.add('hidden'), 500);
      }
    });
  }
 // Listen for hash changes and page load
 window.addEventListener('hashchange', handleRouting);
 window.addEventListener('load', handleRouting);
   // toggle the screen at mobile
const toggle =() =>{
    const hamburger = document.querySelector('.hamburger');
    const navbar = document.querySelector('.navbar');
    const overlay = document.createElement('div');

    // Create an overlay for better UX
    overlay.classList.add('overlay');
    document.body.appendChild(overlay);

    hamburger.addEventListener('click', () => {
        navbar.classList.toggle('show');
        overlay.classList.toggle('show');
        hamburger.classList.toggle('toggled');
    });

    // Close the navbar when overlay is clicked
    overlay.addEventListener('click', () => {
        navbar.classList.remove('show');
        overlay.classList.remove('show');
        hamburger.classList.remove('toggled');
    });

}
toggle();

// end of toggle
// the news bar
const newsBar = () =>{
    const newsContent = document.querySelector('.news-content');
    const newNews = [
        'News Headlines:',
        '💳 Secure Transactions: Your transactions are encrypted for maximum security. Bank with confidence!',
        '🔓 24/7 Access: Manage your account anytime, anywhere with round-the-clock access to your funds!',
        '⚡ Fast Transfers: Send money instantly with our quick and efficient transfer services!',
        '📊 Financial Insights: Track spending, create budgets, and make smarter decisions with our financial tools!',
        '💰 Loan and Credit Services: Get approved quickly for personal loans and business credit at competitive rates!',
    ];

    // Function to add new items
    function updateNews() {
        newsContent.innerHTML = '';
        newNews.forEach(news => {
            const p = document.createElement('p');
            p.textContent = news;
            newsContent.appendChild(p);
        });
    }

    // Call the update function
    updateNews();
}
    newsBar();
// end of news


// script.js
document.addEventListener("DOMContentLoaded", () => {
    const targetSection = document.querySelector(".features-section"); // Target the smaller section

    const revealSection = () => {
      const windowHeight = window.innerHeight;
      const sectionTop = targetSection.getBoundingClientRect().top;
      const sectionBottom = targetSection.getBoundingClientRect().bottom;

      // Trigger animation when any part of the section is in the viewport
      if (sectionTop < windowHeight && sectionBottom > 0) {
        targetSection.classList.add("show");
      } else {
        targetSection.classList.remove("show");
      }
    };

    window.addEventListener("scroll", revealSection);

    // Trigger animation on page load
    revealSection();
  });


// the swiper slide
const swiper = new Swiper('.swiper', {
    direction: 'horizontal',
    loop: true,
    spaceBetween: 30,
    slidesPerView: 'auto',
    centeredSlides: true,
    autoplay: {
        delay: 3000,
      },
    breakpoints: {
        // When the window width is greater than or equal to 550px
        550: {
            slidesPerView: 1,
            spaceBetween: 10,
            
        },
        850:{
            spaceBetween: 30,
            slidesPerView: '1',
        },
        990:{
            spaceBetween: 3,
            slidesPerView: '3',
        },
        1200:{
            spaceBetween: 30,
            slidesPerView: 'auto',
        }
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
        renderBullet: function (index, className) {
            const images = [
                './img/testimonial/ogabashir.jpg',
                './img/testimonial/ogaismail.jpg',
                './img/testimonial/ogamubarak.jpg',
                './img/testimonial/dawaki.jpg',
                './img/testimonial/ogamuhammad.jpg',
                './img/testimonial/muhammadtukur.jpg',
            ];
            return `<span class="${className}"><img src="${images[index]}" alt="Feature ${index + 1}"></span>`;
        },
    },
});
// end of swiper

// the how its work

const howWork = () =>{
    // Get the progress line, bars, and steps
    const progressLine = document.querySelector('.progress-line');
    const bars = document.querySelectorAll('.bar');
    const steps = document.querySelectorAll('.step');
    let clickedStep = 0; // Track the clicked step

    // Define progress percentages for each step
    const progressSteps = {
        1: '0%',
        2: '33%',
        3: '66%',
        4: '100%'
    };

    // Function to update the progress line and associated UI
    function updateProgressBar(step) {

        const isVertical = window.matchMedia('(max-width: 850px)').matches;

        if (isVertical) {
            progressLine.style.height = progressSteps[step];
            progressLine.style.width = '4px';
        } else {
            progressLine.style.width = progressSteps[step];
            progressLine.style.height = '4px';
        }
        progressLine.style.backgroundColor = '#559403';

        bars.forEach(bar => {
            const barStep = parseInt(bar.getAttribute('data-step'), 10);
            bar.style.backgroundColor = barStep <= step ? '#559403' : '#ddd';
        });

        steps.forEach((stepDiv, index) => {
            if (index < step) {
                stepDiv.style.opacity = '1';
                stepDiv.style.transform = isVertical ? 'translateY(0)' : 'translateX(0)';
            } else {
                stepDiv.style.opacity = '0.5';
                stepDiv.style.transform = isVertical ? 'translateY(10px)' : 'translateX(10px)';
            }
        });
    }


    // Add hover listeners to each bar
    bars.forEach(bar => {
        bar.addEventListener('mouseenter', () => {
            if (clickedStep === 0) {
                const step = bar.getAttribute('data-step');
                updateProgressBar(step); // Update progress on hover
            }
        });

        bar.addEventListener('mouseleave', () => {
            if (clickedStep === 0) {
                progressLine.style.width = '0%';
                progressLine.style.backgroundColor = '#ddd';
                bars.forEach(bar => bar.style.backgroundColor = '#ddd');
                steps.forEach(stepDiv => {
                    stepDiv.style.opacity = '0.8';
                    stepDiv.style.transform = 'translateY(0)';
                });
            }
        });

        bar.addEventListener('click', () => {
            const step = bar.getAttribute('data-step');
            clickedStep = step;
            updateProgressBar(step);
        });
    });

}
howWork();
// end of howits work

// the frequent question
const frequent = () =>{
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        item.querySelector('.faq-question').addEventListener('click', () => {
            item.classList.toggle('open');
        });
    });
}
frequent();

// end of frequent

// the scrollto top
const scrollbtn = () =>{
        const scrollToTopBtn = document.getElementById("scrollToTop");

        window.addEventListener("scroll", () => {
            if (window.scrollY > 200) {
                scrollToTopBtn.classList.add("show");
            } else {
                scrollToTopBtn.classList.remove("show");
            }
        });
        scrollToTopBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });

}
scrollbtn();

// end ofscrollbtn

// the call action in about
    const callAction = () =>{
        // Smooth scroll to the "Learn More" section
        document.querySelector('.cta-button').addEventListener('click', function (event) {
            event.preventDefault();
            const target = document.querySelector('#learn-more');
            window.scrollTo({
                top: target.offsetTop,
                behavior: 'smooth'
            });
        });

        // Fade-in effect on scroll for the "Learn More" section
        const learnMoreSection = document.querySelector('#learn-more');
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        learnMoreSection.style.opacity = 1;
                        learnMoreSection.style.transform = 'translateY(0)';
                    }
                });
            },
            { threshold: 0.1 }
        );

        learnMoreSection.style.opacity = 0;
        learnMoreSection.style.transform = 'translateY(30px)';
        observer.observe(learnMoreSection);


    }
    callAction();
// end of call action in about

// form contact in contact page
const formContact = () =>{
    let currentStep = 0;
     const steps = document.querySelectorAll('.form-step');
const nextStep = document.getElementById("nextStep").addEventListener("click", function() {
            if (currentStep < steps.length - 1) {

                steps[currentStep].classList.remove('active');
                currentStep++;
                steps[currentStep].classList.add('active');
            }
});
    const prevStep = document.getElementById("prevStep").addEventListener("click", function (){
                if (currentStep > 0) {
                    steps[currentStep].classList.remove('active');
                    currentStep--;
                    steps[currentStep].classList.add('active');
                }

    });
}
formContact();
