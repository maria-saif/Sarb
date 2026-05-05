<?php


/**
 * 
 * @param string $text النص المستخلص من الصوت
 * @param array $weights مصفوفة كلمات/أوزان (يمكن تركها فارغة)
 * @return array النتائج
 */
function vs_analyze_text($text, $weights = []) {
    $suspicious_keywords = [
    'احتيال', 'عملية احتيال', 'احتيالي', 'سرقة هوية', 
    'شرطة', 'شرطة عمان السلطانية', 
    'محكمة', 'دعوى', 'قضية',
    'جرائم إلكترونية', 'قسم الجرائم الإلكترونية',
    'مذكرة توقيف', 'أمر قبض', 'القبض',
    'تحقيق رسمي', 'تحقيق جنائي',
    'تأكيد الهوية', 'تحقق من هويتك',
    'بلاغ رسمي',
    'تجميد الحساب', 'تم تجميد حسابك', 'تم حظر حسابك',
    'إيقاف الحساب', 'إغلاق الحساب',
    'تحويل مالي كبير', 'تحويل فوري',
    'مصادرة أموال', 'حجز أموال',
    'إجراء قانوني', 'إجراءات قانونية',
    
    'fraud', 'bank fraud', 'identity theft',
    'criminal investigation', 'police',
    'arrest warrant', 'arrest',
    'prosecution',
    'court', 'lawsuit',
    'frozen account', 'account frozen', 'account blocked',
    'legal action', 'legal actions',
    'confiscated funds',
    'otp', 'security code',
    'wire transfer',
    
    'حساب', 'حسابك', 'حسابك البنكي', 'البنك', 
    'التحقق', 'تحقق', 'نشاط مشبوه', 'وصول غير مصرح', 
    'محاولة تسجيل دخول', 'كشف الحساب', 
    'تحويل', 'تحويل بنكي',
    'account', 'verify', 'suspended', 'unauthorized access', 'suspicious activity', 'payment declined',
    
    'فاتورة', 'فواتير', 'رسوم', 'دفع', 'خدمة العملاء', 'اشتراك', 'استرجاع', 'استعادة الأموال',
    'invoice', 'payment', 'fee', 'refund', 'customer care', 'remote access',
    
    'عاجل', 'مهم', 'اتصل الآن', 'دعم', 'urgent', 'important', 'technical support', 'press 1',
    
    'عرض خاص', 'مجاني', 'بطاقة هدية', 'free', 'limited time', 'gift card',
    
    'hello', 'welcome', 'service'
];

    $found = [];
    $lower_text = mb_strtolower($text, 'UTF-8');
    foreach ($suspicious_keywords as $kw) {
        if (mb_stripos($lower_text, $kw) !== false) {
            $found[] = $kw;
        }
    }


    $word_count = max(1, str_word_count($text));
    $risk_percent = min(100, round((count($found) / $word_count) * 100 * 2));

    if ($risk_percent >= 70) $category = "🔴 High Risk";
    elseif ($risk_percent >= 40) $category = "🟠 Suspicious";
    elseif ($risk_percent >= 20) $category = "🟡 Low Risk";
    else $category = "✅ Safe";

    return [
        "keywords" => $found,
        "risk" => $risk_percent,
        "category" => $category,
        "actions" => count($found) > 0 ? "Review suspicious content" : "No action needed"
    ];
}
?>