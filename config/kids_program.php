<?php

return [
    'daily_devotions' => [
        'monday' => [
            'title' => 'Kasih Dimulai dari Rumah',
            'verse' => 'Yohanes 13:34',
            'message' => 'Yesus mengajar kita saling mengasihi. Mulai dari rumah: bicara lembut, bantu orang tua, dan jadi pembawa damai.',
            'challenge' => 'Lakukan 1 tindakan kasih di rumah tanpa disuruh.',
        ],
        'tuesday' => [
            'title' => 'Berani Karena Tuhan',
            'verse' => 'Yosua 1:9',
            'message' => 'Saat kamu takut, ingat Tuhan berjalan bersamamu. Kamu bisa tetap jujur dan berani melakukan yang benar.',
            'challenge' => 'Doakan hal yang kamu takuti lalu ceritakan pada orang tua.',
        ],
        'wednesday' => [
            'title' => 'Hati yang Taat',
            'verse' => 'Efesus 6:1',
            'message' => 'Ketaatan bukan kelemahan, melainkan bukti kasih kepada Tuhan. Taat membuat hati bertumbuh dewasa.',
            'challenge' => 'Pilih 1 instruksi orang tua dan kerjakan dengan sukacita.',
        ],
        'thursday' => [
            'title' => 'Syukur di Segala Keadaan',
            'verse' => '1 Tesalonika 5:18',
            'message' => 'Bersyukur membuka mata kita melihat kebaikan Tuhan setiap hari, bahkan lewat hal kecil.',
            'challenge' => 'Tulis 3 hal yang kamu syukuri hari ini.',
        ],
        'friday' => [
            'title' => 'Belajar Mengampuni',
            'verse' => 'Kolose 3:13',
            'message' => 'Mengampuni memang tidak selalu mudah, tapi Tuhan memberi kekuatan agar hati kita tetap damai.',
            'challenge' => 'Maafkan teman/saudara yang membuatmu kesal minggu ini.',
        ],
        'saturday' => [
            'title' => 'Tetap Semangat Berbuat Baik',
            'verse' => 'Galatia 6:9',
            'message' => 'Kebaikan yang kecil kalau dilakukan terus akan jadi berkat besar untuk banyak orang.',
            'challenge' => 'Lakukan 1 kebaikan rahasia untuk seseorang.',
        ],
        'sunday' => [
            'title' => 'Bersukacita Beribadah',
            'verse' => 'Mazmur 122:1',
            'message' => 'Hari Tuhan adalah hari sukacita. Datang beribadah dengan hati siap memuji dan belajar Firman.',
            'challenge' => 'Hafalkan 1 ayat hari ini dan ucapkan di rumah.',
        ],
    ],

    'weekly_badges' => [
        ['label' => 'Faith Starter', 'min_score' => 0],
        ['label' => 'Faith Explorer', 'min_score' => 220],
        ['label' => 'Faith Hero', 'min_score' => 420],
        ['label' => 'Faith Champion', 'min_score' => 560],
    ],

    'quiz_sets' => [
        'monday' => [
            'title' => 'Kasih Yesus',
            'memory_verse' => 'Yohanes 13:34',
            'questions' => [
                ['id' => 'm1', 'question' => 'Yesus berkata kita harus saling ...', 'options' => ['membandingkan', 'mengasihi', 'menghindari', 'menghakimi'], 'answer' => 'mengasihi'],
                ['id' => 'm2', 'question' => 'Kasih yang benar terlihat lewat ...', 'options' => ['kata saja', 'tindakan baik', 'diam saja', 'marah'], 'answer' => 'tindakan baik'],
                ['id' => 'm3', 'question' => 'Saat teman sedih, sikap yang benar adalah ...', 'options' => ['mengejek', 'mendoakan dan menolong', 'membiarkan', 'memarahi'], 'answer' => 'mendoakan dan menolong'],
            ],
        ],
        'tuesday' => [
            'title' => 'Keberanian Dalam Tuhan',
            'memory_verse' => 'Yosua 1:9',
            'questions' => [
                ['id' => 't1', 'question' => 'Firman Tuhan berkata: jangan ...', 'options' => ['berdoa', 'takut', 'belajar', 'berbagi'], 'answer' => 'takut'],
                ['id' => 't2', 'question' => 'Saat takut, kita perlu ...', 'options' => ['lari dari Tuhan', 'berdoa', 'menyerah', 'berbohong'], 'answer' => 'berdoa'],
                ['id' => 't3', 'question' => 'Tuhan menyertai kita di ...', 'options' => ['gereja saja', 'rumah saja', 'setiap tempat', 'hari minggu saja'], 'answer' => 'setiap tempat'],
            ],
        ],
        'wednesday' => [
            'title' => 'Hati Yang Taat',
            'memory_verse' => 'Efesus 6:1',
            'questions' => [
                ['id' => 'w1', 'question' => 'Anak-anak diminta taat kepada ...', 'options' => ['teman', 'orang tua', 'tetangga', 'siapa saja'], 'answer' => 'orang tua'],
                ['id' => 'w2', 'question' => 'Taat adalah sikap yang ...', 'options' => ['menyenangkan Tuhan', 'membuat ribut', 'egois', 'tidak penting'], 'answer' => 'menyenangkan Tuhan'],
                ['id' => 'w3', 'question' => 'Contoh taat di rumah adalah ...', 'options' => ['membantu merapikan kamar', 'membantah', 'berbohong', 'malas'], 'answer' => 'membantu merapikan kamar'],
            ],
        ],
        'thursday' => [
            'title' => 'Syukur Setiap Hari',
            'memory_verse' => '1 Tesalonika 5:18',
            'questions' => [
                ['id' => 'th1', 'question' => 'Kita diminta bersyukur dalam ...', 'options' => ['keadaan apa pun', 'saat dapat hadiah saja', 'hari minggu saja', 'waktu senang saja'], 'answer' => 'keadaan apa pun'],
                ['id' => 'th2', 'question' => 'Ucapan syukur membuat hati jadi ...', 'options' => ['keras', 'pahit', 'penuh sukacita', 'takut'], 'answer' => 'penuh sukacita'],
                ['id' => 'th3', 'question' => 'Cara bersyukur sederhana adalah ...', 'options' => ['mengeluh', 'berdoa terima kasih', 'membandingkan', 'marah'], 'answer' => 'berdoa terima kasih'],
            ],
        ],
        'friday' => [
            'title' => 'Mengampuni Seperti Yesus',
            'memory_verse' => 'Kolose 3:13',
            'questions' => [
                ['id' => 'f1', 'question' => 'Jika teman melakukan salah, kita diajar untuk ...', 'options' => ['balas dendam', 'mengampuni', 'menghindari selamanya', 'menyebarkan gosip'], 'answer' => 'mengampuni'],
                ['id' => 'f2', 'question' => 'Mengampuni menolong hati menjadi ...', 'options' => ['damai', 'gelisah', 'takut', 'marah terus'], 'answer' => 'damai'],
                ['id' => 'f3', 'question' => 'Yesus lebih dulu ... kita', 'options' => ['menghakimi', 'mengampuni', 'meninggalkan', 'melupakan'], 'answer' => 'mengampuni'],
            ],
        ],
        'saturday' => [
            'title' => 'Rajin Berbuat Baik',
            'memory_verse' => 'Galatia 6:9',
            'questions' => [
                ['id' => 's1', 'question' => 'Kita tidak boleh jemu berbuat ...', 'options' => ['jahat', 'baik', 'asal-asalan', 'suka-suka'], 'answer' => 'baik'],
                ['id' => 's2', 'question' => 'Berbuat baik bisa dimulai dari ...', 'options' => ['hal kecil', 'hal besar saja', 'nanti saja', 'orang lain dulu'], 'answer' => 'hal kecil'],
                ['id' => 's3', 'question' => 'Menolong teman belajar adalah contoh ...', 'options' => ['kemalasan', 'kebaikan', 'persaingan', 'kesombongan'], 'answer' => 'kebaikan'],
            ],
        ],
        'sunday' => [
            'title' => 'Sukacita Beribadah',
            'memory_verse' => 'Mazmur 122:1',
            'questions' => [
                ['id' => 'su1', 'question' => 'Daud bersukacita saat diajak ke ...', 'options' => ['pasar', 'rumah Tuhan', 'taman', 'sekolah'], 'answer' => 'rumah Tuhan'],
                ['id' => 'su2', 'question' => 'Ibadah membantu kita ... Tuhan', 'options' => ['melupakan', 'mengenal', 'menjauh dari', 'takut pada'], 'answer' => 'mengenal'],
                ['id' => 'su3', 'question' => 'Sikap saat ibadah sebaiknya ...', 'options' => ['bercanda terus', 'fokus dan hormat', 'sibuk main sendiri', 'mengantuk terus'], 'answer' => 'fokus dan hormat'],
            ],
        ],
    ],

    'mini_games' => [
        ['title' => 'Tebak Tokoh Alkitab', 'description' => 'Baca petunjuk singkat, lalu tebak tokohnya.'],
        ['title' => 'Misi Kasih Acak', 'description' => 'Putar misi dan lakukan tindakan kasih hari ini.'],
        ['title' => 'Susun Ayat Cepat', 'description' => 'Susun kata ayat hafalan jadi urutan yang benar.'],
    ],
];
