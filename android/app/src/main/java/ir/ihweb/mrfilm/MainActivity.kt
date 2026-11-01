package ir.ihweb.mrfilm

import android.annotation.SuppressLint
import android.app.Activity
import android.os.Bundle
import android.webkit.*
import android.graphics.Color
import android.view.ViewGroup

class MainActivity : Activity() {
    private lateinit var web: WebView
    private val appHost = "ac.m.ihweb.ir"
    private val appUrl = "https://ac.m.ihweb.ir/app/"
    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        window.statusBarColor = Color.rgb(11,16,25)
        web = WebView(this)
        web.layoutParams = ViewGroup.LayoutParams(-1,-1)
        web.setBackgroundColor(Color.rgb(11,16,25))
        web.settings.javaScriptEnabled = true
        web.settings.domStorageEnabled = true
        web.settings.allowFileAccess = false
        web.settings.allowContentAccess = false
        web.settings.mixedContentMode = WebSettings.MIXED_CONTENT_NEVER_ALLOW
        web.settings.javaScriptCanOpenWindowsAutomatically = false
        web.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView?, request: WebResourceRequest?): Boolean {
                val u = request?.url ?: return true
                return !(u.scheme == "https" && u.host == appHost)
            }
        }
        web.webChromeClient = WebChromeClient()
        setContentView(web)
        if (savedInstanceState == null) web.loadUrl(appUrl) else web.restoreState(savedInstanceState)
    }
    override fun onSaveInstanceState(outState: Bundle) { super.onSaveInstanceState(outState); web.saveState(outState) }
    @Deprecated("Deprecated in Java")
    override fun onBackPressed() { if (this::web.isInitialized && web.canGoBack()) web.goBack() else super.onBackPressed() }
}
