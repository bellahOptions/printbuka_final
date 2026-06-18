// images
import blogImg1 from "../img/news/01.jpg";
import blogImg2 from "../img/news/02.jpg";
import blogImg3 from "../img/news/03.jpg";

import blogSingleImg1 from "../img/news/post-1.jpg";
import blogSingleImg2 from "../img/news/post-2.jpg";
import blogSingleImg3 from "../img/news/post-3.jpg";



const blogs = [
    {
        id: '1',
        title: 'How to Prepare Your Artwork File for Print — A Complete Guide',
        slug: 'how-to-prepare-artwork-file-for-print',
        screens: blogImg1,
        description: 'Setting up your artwork correctly saves time and avoids costly reprints. Learn the key file specs Printbuka requires for perfect results every time.',
        author: 'Printbuka Team',
        create_at: '10 Jun 2025',
        blogSingleImg: blogSingleImg1,
        comment: '12',
        day: '10',
        month: 'JUNE',
        blClass: 'format-standard-image',
        animation: '1200',
    },
    {
        id: '2',
        title: 'UV-DTF vs DTF Printing — Which is Right for Your Product?',
        slug: 'uv-dtf-vs-dtf-printing-comparison',
        screens: blogImg2,
        description: 'Both UV-DTF and DTF transfers produce stunning results — but they are suited to very different surfaces. We break down the differences to help you choose.',
        author: 'Printbuka Team',
        create_at: '28 May 2025',
        blogSingleImg: blogSingleImg2,
        comment: '8',
        day: '28',
        month: 'MAY',
        blClass: 'format-standard-image',
        animation: '1400',
    },
    {
        id: '3',
        title: '10 Branded Gift Ideas That Will Impress Your Clients in 2025',
        slug: 'branded-gift-ideas-2025',
        screens: blogImg3,
        description: 'Corporate gifting is a powerful brand touchpoint. Here are 10 branded merchandise ideas — all available at Printbuka — that clients will actually use and remember.',
        author: 'Printbuka Team',
        create_at: '15 May 2025',
        blogSingleImg: blogSingleImg3,
        comment: '21',
        day: '15',
        month: 'MAY',
        blClass: 'format-video',
        animation: '1600',
    }
];
export default blogs;
