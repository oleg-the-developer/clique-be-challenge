export default {
    init() {
        // JavaScript to be fired on all pages
        "use strict";

        document.addEventListener("DOMContentLoaded", () => {
            console.log('ready');
            homeLinkScroll(); // header logo scroll
            smoothScroll(); // smooth scrolling
            setFocus(); // prevent focus on click
            lazyLoading(); // lazy load images
            // setInitialHeaderTop(); // set header initial top position
            toggleFaqs(); // toggle FAQs
            pageNav(); // toggle mobile menu/page navigation
        });

        $(document).resize(function () {
            pageNav(); // toggle mobile menu/page navigation
            mobileToolTip(); // mobile tooltip
        });

        $(window).scroll(function () {
            if (!page.hasClass('fixed')) {
                scrollPos = $(window).scrollTop(); // set scroll position only when body isn't fixed
            }
            // setHeaderTop(); // set header top position on scroll
            toggleToTopBtn(); // show/hide to top button on scroll
            updateNavLinks(); // update current nav link on scroll
        });

        /* variables */
        const page = $('html, body');
        let scrollPos;

        /* functions */

        // Scroll to the top of the page on the homepage, otherwise go to the homepage
        const homeLinkScroll = () => {
            const homeLinkElm = document.querySelector('.home-link');
            homeLinkElm.addEventListener('click', elm => {
                const elmTarget = elm.currentTarget;
                if (elmTarget.classList.contains('on-homepage')) {
                    elm.preventDefault();
                    pageAnimate('#main');
                }
            });
        }

        // smooth scroll
        const smoothScroll = () => {
            $('a[href*="#"]').on('click', function (e) {
                if ($(this).attr('href') !== '#') {
                    e.preventDefault();
                    pageAnimate($(this).attr('href'));
                }
            });
        }

        const pageAnimate = (elm) => {
            // dynamically get height of header
            let scrollTopOffset = 0;
            let scrollOffset = document.getElementById('header').clientHeight - scrollTopOffset;
            page.animate({
                scrollTop: $(elm).offset().top - scrollOffset
            }, 250, 'swing');
        };

        // mobile tooltip
        const mobileToolTip = () => {
			if (window.innerWidth < 1025) {
				$('.legal').on('click touchstart', function(e){
					e.preventDefault();
					$(this).next().show();
					$(this).next().next().show();
				});
				$('.tooltip .close').on('click touchstart', function(e){
					e.preventDefault();
					$(this).parent().hide();
					$(this).parent().next().hide();
				});
			} else {
				$('.legal').off();
				$('.legal').on('click touchstart', function(e){
					e.preventDefault();
				});
			}
		};

        // prevent focus on click
        const setFocus = () => {
            $('a, button').click(function () {
                $(this).addClass('active');
            }).blur(function () {
                $(this).removeClass('active');
            });
        }

        // lazy load images
        const lazyLoading = () => {
            const aboutElm = document.querySelector('.home__about');
            const downloadElm = document.querySelector('.home__download');
            const whyElm = document.querySelector('.home__why');

            if (aboutElm) {
                const aboutWaypoint = new Waypoint({
                    element: aboutElm,
                    handler: function (direction) {
                        $(this.element).find('img[data-src]').each(function () {
                            let lazyImgSrc = $(this).attr('data-src');
                            $(this).attr('src', lazyImgSrc).addClass('fadein');
                        });
                    },
                    offset: '110%'
                });
            }
            if (downloadElm) {
                const downloadWaypoint = new Waypoint({
                    element: downloadElm,
                    handler: function (direction) {
                        $(this.element).find('img[data-src]').each(function () {
                            let lazyImgSrc = $(this).attr('data-src');
                            $(this).attr('src', lazyImgSrc).addClass('fadein');
                        });

                        $(this.element).find('source').each(function () {
                            let lazyImgMediaSrc = $(this).attr('data-srcset');
                            $(this).attr('srcset', lazyImgMediaSrc);
                        });
                    },
                    offset: '110%'
                });
            }
            if (whyElm) {
                const whyWaypoint = new Waypoint({
                    element: whyElm,
                    handler: function (direction) {
                        let lazyImg = $('.home__why__image img');
                        let lazyImgSrc = lazyImg.attr('data-src');
                        lazyImg.attr('src', lazyImgSrc).addClass('fadein');

                        let lazySource = $('.home__why__image source');
                        lazySource.each(function () {
                            let lazyImgMediaSrc = $(this).attr('data-srcset');
                            $(this).attr('srcset', lazyImgMediaSrc);
                        });
                    },
                    offset: '110%'
                });
            }
        }

        // const headerHeight = 70;
        // set header initial top position
        const setInitialHeaderTop = () => {
            const headerMain = $('.header__main');
            // headerMain.css('top', '40px');
        }

        const setHeaderTop = () => {
            const header = $('.header'),
                modal = $('.eligibility-modal');

            // if (scrollPos > 0) {
            //     header.css('top', `-${headerHeight}px`);
            // } else if (modal.hasClass('open')) {
            //     header.css('top', 0);
            // } else {
            //     header.css('top', 0);
            // }
        }

        const toggleToTopBtn = () => {
            const topBtn = $('.top');

            if (scrollPos <= $(window).height()) {
                topBtn.fadeOut(300);
            } else {
                topBtn.fadeIn(300);
            }
        }

        //toggle faqs
        const toggleFaqs = () => {
            const faqQuestion = $('.question'),
                faqAnswer = $('.answer'),
                faqExpandAll = $('#expand'),
                faqCollapseAll = $('#collapse');

            faqQuestion.click(function () {
                if ($(this).hasClass('open')) {
                    $(this).removeClass('open').next().slideUp(300);
                } else {
                    $(this).addClass('open').next().slideDown(300);
                }
                faqCollapseAll.removeClass('active');
                faqExpandAll.removeClass('active');
            });

            faqExpandAll.click(function () {
                faqCollapseAll.removeClass('active');
                $(this).addClass('active');
                faqQuestion.addClass('open');
                faqAnswer.slideDown(300);
            });

            faqCollapseAll.click(function () {
                faqExpandAll.removeClass('active');
                $(this).addClass('active');
                faqQuestion.removeClass('open');
                faqAnswer.slideUp(300);
            });
        }

        const updateNavLinks = () => {
            let sections = $('section');
            sections.each(function () {
                let section = $(this),
                    sectionOffset = section.offset(),
                    sectionHeight = section.height();

                if (sectionOffset.top - 140 <= scrollPos && sectionOffset.top + sectionHeight > scrollPos) {
                    let sectionHash = section.attr('id'),
                        navLinks = $('.header__main__nav li a'),
                        relatedNavLink = $('a[href$="' + sectionHash + '"]');

                    navLinks.removeClass('current');
                    relatedNavLink.addClass('current');
                }
            });
        }

        // toggle mobile menu/page navigation
        const pageNav = () => {
            const mobileMenuToggle = $('.header__main__mobile-toggle'),
                header = $('.header'),
                menuContainer = $('.header__main'),
                menu = $('.header__main__nav'),
                menuLinks = $('.header__main__nav li a, .header__main__nav .btn');

            mobileMenuToggle.click(function () {
                $(this).toggleClass('is-active');
                menuContainer.toggleClass('is-active');
                menu.toggleClass('is-active');
                page.toggleClass('fixed');
                header.css('top', '0');
            });

            menuLinks.click(function () {
                if (!$(this).hasClass('eligibility-btn')) {
                    mobileMenuToggle.removeClass('is-active');
                    menuContainer.removeClass('is-active');
                    menu.removeClass('is-active');
                    page.removeClass('fixed');
                }
            });
        }
    },
    finalize() {
        // JavaScript to be fired on all pages, after page specific JS is fired
    },
};
