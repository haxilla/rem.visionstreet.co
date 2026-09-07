@include('member.layout.head', ['pageTitle' => 'Account Info | Realty Emails'])

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@include('member.layout.comingSoon', [
    'title' => 'Account Info',
    'description' => 'Start date, account type, credits, username/password, and purchase history will live here.',
])

@include('public.layout.footer')

</body>
</html>
