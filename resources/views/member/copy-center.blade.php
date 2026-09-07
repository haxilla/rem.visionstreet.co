@include('member.layout.head', ['pageTitle' => 'Copy Center | Realty Emails'])

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@include('member.layout.comingSoon', [
    'title' => 'Copy Center',
    'description' => 'Ready-made marketing copy and remark templates you can drop into a flyer will live here.',
])

@include('public.layout.footer')

</body>
</html>
