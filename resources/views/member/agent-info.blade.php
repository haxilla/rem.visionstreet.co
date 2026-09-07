@include('member.layout.head', ['pageTitle' => 'Agent Info | Realty Emails'])

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@include('member.layout.comingSoon', [
    'title' => 'Agent Info',
    'description' => 'Your profile photo, name, designations, and office details will be editable here.',
])

@include('public.layout.footer')

</body>
</html>
