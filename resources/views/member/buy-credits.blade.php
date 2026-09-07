@include('member.layout.head', ['pageTitle' => 'Buy Credits | Realty Emails'])

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@include('member.layout.comingSoon', [
    'title' => 'Buy Credits',
    'description' => 'Purchasing additional flyer/send credits will happen here.',
])

@include('public.layout.footer')

</body>
</html>
