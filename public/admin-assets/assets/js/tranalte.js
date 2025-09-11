// English ↔ Bangla number map
const enToBnNums = {0:"০",1:"১",2:"২",3:"৩",4:"৪",5:"৫",6:"৬",7:"৭",8:"৮",9:"৯"};
const bnToEnNums = {"০":"0","১":"1","২":"2","৩":"3","৪":"4","৫":"5","৬":"6","৭":"7","৮":"8","৯":"9"};

// 🔄 English → Bangla words
const enToBnWords = {
  // General UI
  "Logo": "লোগো",
  "Dashboard": "ড্যাশবোর্ড",
  "Admin Panel": "অ্যাডমিন প্যানেল",
  "SMS and Email": "এসএমএস এবং ইমেইল",
  "Contact": "যোগাযোগ",
  "Income": "আয়",
  "Expense": "ব্যয়",
  "Investments Management": "বিনিয়োগ ব্যবস্থাপনা",
  "Incomes Managment": "আয় ব্যবস্থাপনা",
  "Expenses Managment": "ব্যয় ব্যবস্থাপনা",
  "Assets Management": "সম্পদ ব্যবস্থাপনা",
  "Liabilities Management": "দায় ব্যবস্থাপনা",
  "BankBooks Management": "ব্যাংকবুকস ব্যবস্থাপনা",
  "Accounts": "অ্যাকাউন্ট",
  "Last login": "সর্বশেষ লগইন",
  "Software Version": "সফটওয়্যার সংস্করণ",
  "Welcome again": "আবার স্বাগতম",
  "Good Evening": "শুভ সন্ধ্যা",
  "Today": "আজ",
  "Upcoming Events": "আসন্ন ইভেন্টসমূহ",
  "Quick Posting": "দ্রুত পোস্টিং",
  "This Month Income": "এই মাসের আয়",
  "This Month Expenses": "এই মাসের ব্যয়",
  "This Month Assets": "এই মাসের সম্পদ",
  "This Month Liabilities": "এই মাসের দায়",
  "This Month Investments": "এই মাসের বিনিয়োগ",
  "This Month Bank Amount": "এই মাসের ব্যাংক ব্যালান্স",
  "Total Incomes": "মোট আয়",
  "Total Expenses": "মোট ব্যয়",
  "Total Assets": "মোট সম্পদ",
  "Total Liability": "মোট দায়",
  "Total Investments": "মোট বিনিয়োগ",
  "Total Bank Amount": "মোট ব্যাংক ব্যালান্স",
  "Transaction Statistics": "লেনদেনের পরিসংখ্যান",
  "Monthly Statistics": "মাসিক পরিসংখ্যান",
  
  "Asset": "সম্পদ",
  "Liability": "দায়",
  "Total Revenue": "মোট রাজস্ব",
  "Bank Transactions": "ব্যাংক লেনদেন",
  "User": "ব্যবহারকারী",
  "Savings Account": "সঞ্চয়ী অ্যাকাউন্ট",
  "credit": "জমা",
  
  "N/A": "প্রযোজ্য নয়",
    "Settings": "সেটিংস",
    " Management": "ব্যবস্থাপনা",
  // Currencies & greetings
  "BDT": "টাকা",
  "Hello": "হ্যালো",

  // Time
  "AM": "এএম",
  "PM": "পিএম",

  // English Months
  "Jan": "জানুয়ারি",
  "Feb": "ফেব্রুয়ারি",
  "Mar": "মার্চ",
  "Apr": "এপ্রিল",
  "May": "মে",
  "Jun": "জুন",
  "Jul": "জুলাই",
  "Aug": "আগস্ট",
  "Sep": "সেপ্টেম্বর",
  "Oct": "অক্টোবর",
  "Nov": "নভেম্বর",
  "Dec": "ডিসেম্বর",
  "January": "জানুয়ারি",
  "February": "ফেব্রুয়ারি",
  "March": "মার্চ",
  "April": "এপ্রিল",
  "May": "মে",
  "June": "জুন",
  "July": "জুলাই",
  "August": "আগস্ট",
  "September": "সেপ্টেম্বর",
  "October": "অক্টোবর",
  "November": "নভেম্বর",
  "December": "ডিসেম্বর",
"This Month" : "এই মাসের",
"Add New Income": "নতুন আয় যোগ করুন",
"Add New Expense": "নতুন ব্যয় যোগ করুন",
"Asset Options": "সম্পদ অপশন",
"Liability Options": "দায় অপশন",
"Investment Options": "বিনিয়োগ অপশন",
"Add New Bank Transaction": "নতুন ব্যাংক লেনদেন যোগ করুন",
"Saturday": "শনিবার",
"Sunday": "রবিবার",
"Monday": "সোমবার",
"Tuesday": "মঙ্গলবার",
"Wednesday": "বুধবার",
"Thursday": "বৃহস্পতিবার",
"Friday": "শুক্রবার",
};

// 🔄 Bangla → English words
const bnToEnWords = {
  "লোগো": "Logo",
  "ড্যাশবোর্ড": "Dashboard",
  "অ্যাডমিন প্যানেল": "Admin Panel",
  "এসএমএস এবং ইমেইল": "SMS and Email",
  "যোগাযোগ": "Contact",
  "বিনিয়োগ ব্যবস্থাপনা": "Investments Management",
  "আয় ব্যবস্থাপনা": "Incomes Management",
  "ব্যয় ব্যবস্থাপনা": "Expenses Management",
  "সম্পদ ব্যবস্থাপনা": "Assets Management",
  "দায় ব্যবস্থাপনা": "Liabilities Management",
  "ব্যাংকবুকস ব্যবস্থাপনা": "BankBooks Management",
  "অ্যাকাউন্ট": "Accounts",
  "সর্বশেষ লগইন": "Last login",
  "সফটওয়্যার সংস্করণ": "Software Version",
  "আবার স্বাগতম": "Welcome again",
  "শুভ সন্ধ্যা": "Good Evening",
  "আজ": "Today",
  "আসন্ন ইভেন্টসমূহ": "Upcoming Events",
  "দ্রুত পোস্টিং": "Quick Posting",
  "এই মাসের আয়": "This Month Income",
  "এই মাসের ব্যয়": "This Month Expenses",
  "এই মাসের সম্পদ": "This Month Assets",
  "এই মাসের দায়": "This Month Liabilities",
  "এই মাসের বিনিয়োগ": "This Month Investments",
  "এই মাসের ব্যাংক ব্যালান্স": "This Month Bank Amount",
  "এই মাসের" : "This Month",
  "মোট আয়": "Total Income",
  "মোট ব্যয়": "Total Expense",
  "মোট সম্পদ": "Total Assets",
  "মোট দায়": "Total Liability",
  "মোট বিনিয়োগ": "Total Investments",
  "মোট ব্যাংক ব্যালান্স": "Total Bank Amount",
  "লেনদেনের পরিসংখ্যান": "Transaction Statistics",
  "মাসিক পরিসংখ্যান": "Monthly Statistics",
  "আয়": "Income",
  "ব্যয়": "Expense",
  "সম্পদ": "Asset",
  "দায়": "Liability",
  "মোট রাজস্ব": "Total Revenue",
  "ব্যাংক লেনদেন": "Bank Transactions",
  "ব্যবহারকারী": "User",
  "সঞ্চয়ী অ্যাকাউন্ট": "Savings Account",
  "জমা": "credit",
  
  "প্রযোজ্য নয়": "N/A",
    "সেটিংস": "Settings",
    "ব্যবস্থাপনা": "Management",

  "টাকা": "BDT",
  "হ্যালো": "Hello",

  // Time
  "এএম": "AM",
  "পিএম": "PM",

  // Bangla Months
  "জানুয়ারি": "Jan",
  "ফেব্রুয়ারি": "Feb",
  "মার্চ": "Mar",
  "এপ্রিল": "Apr",
  "মে": "May",
  "জুন": "Jun",
  "জুলাই": "Jul",
  "আগস্ট": "Aug",
  "সেপ্টেম্বর": "Sep",
  "অক্টোবর": "Oct",
  "নভেম্বর": "Nov",
  "ডিসেম্বর": "Dec",
  "জানুয়ারি": "January",
  "ফেব্রুয়ারি": "February",
  "মার্চ": "March",
  "এপ্রিল": "April",
  "মে": "May",
  "জুন": "June",
  "জুলাই": "July",
  "আগস্ট": "August",
  "সেপ্টেম্বর": "September",
  "অক্টোবর": "October",
  "নভেম্বর": "November",
  "ডিসেম্বর": "December",
    "এই মাসের": "This Month",
    "নতুন আয় যোগ করুন": "Add New Income",
    "নতুন ব্যয় যোগ করুন": "Add New Expense",
    "সম্পদ অপশন": "Asset Options",
    "দায় অপশন": "Liability Options",
    "বিনিয়োগ অপশন": "Investment Options",
    "নতুন ব্যাংক লেনদেন যোগ করুন": "Add New Bank Transaction",
    "শনিবার": "Saturday",
    "রবিবার": "Sunday",
    "সোমবার": "Monday",
    "মঙ্গলবার": "Tuesday",
    "বুধবার": "Wednesday",
    "বৃহস্পতিবার": "Thursday",
    "শুক্রবার": "Friday",
};


let currentLang = localStorage.getItem("lang") || "en"; // Load saved language or default en

function translateText(text, toLang){
  if(toLang === "bn"){
    // Convert numbers
    text = text.replace(/\d/g, d => enToBnNums[d] || d);

    // 1. First translate multi-word phrases (longest matches first)
    Object.keys(enToBnWords).sort((a, b) => b.length - a.length).forEach(en => {
      const regex = new RegExp(en, "g"); // no word boundary so phrases match
      text = text.replace(regex, enToBnWords[en]);
    });

  } else {
    // Convert Bangla numbers → English
    text = text.replace(/[০-৯]/g, d => bnToEnNums[d] || d);

    // 2. Reverse translate multi-word phrases first
    Object.keys(bnToEnWords).sort((a, b) => b.length - a.length).forEach(bn => {
      const regex = new RegExp(bn, "g");
      text = text.replace(regex, bnToEnWords[bn]);
    });
  }
  return text;
}

function walkAndTranslate(node, toLang){
  if(node.nodeType === 3){
    node.nodeValue = translateText(node.nodeValue, toLang);
  } else {
    node.childNodes.forEach(child => walkAndTranslate(child, toLang));
  }
}

// Apply language everywhere
function applyLanguage(lang){
  currentLang = lang;
  document.documentElement.setAttribute("lang", lang);
  document.documentElement.setAttribute("data-lang", lang);
  walkAndTranslate(document.body, lang);

  // Update button text
  const btn = document.querySelector("#toggleLang span");
  if (btn) {
    btn.textContent = lang === "en" ? "বাংলা" : "English";
  }
}

// Toggle language on button click
document.getElementById("toggleLang").addEventListener("click", () => {
  currentLang = (currentLang === "en") ? "bn" : "en";
  localStorage.setItem("lang", currentLang);
  applyLanguage(currentLang);
});

// Run once on page load
applyLanguage(currentLang);

// ✅ MutationObserver to auto-translate new content (charts, AJAX, etc.)
const observer = new MutationObserver(mutations => {
  mutations.forEach(mutation => {
    mutation.addedNodes.forEach(node => {
      if (node.nodeType === 1) { // element
        walkAndTranslate(node, currentLang);
      } else if (node.nodeType === 3) { // text
        node.nodeValue = translateText(node.nodeValue, currentLang);
      }
    });
  });
});

// Observe entire body for changes
observer.observe(document.body, {
  childList: true,
  subtree: true
});
