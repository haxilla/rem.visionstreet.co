@include('member.layout.head', ['pageTitle' => 'Campaigns | Realty Emails'])

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@include('member.layout.comingSoon', [
    'title' => 'Campaigns',
    'description' => 'A history of your sent email campaigns and their performance will live here.',
])

@include('public.layout.footer')

</body>
</html>
