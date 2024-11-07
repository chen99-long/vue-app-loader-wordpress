async function appendFunction(cdnBaseUrl) {
    const cdnUrl = `${cdnBaseUrl}index.html`;
    
    try {
        const response = await fetch(cdnUrl);
        const htmlText = await response.text();
        
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');
        
        const scripts = Array.from(doc.querySelectorAll('script')).map(script => ({
            src: new URL(script.getAttribute('src'), cdnBaseUrl).href,
            type: script.getAttribute('type') || 'text/javascript'
        }));
        const styles = Array.from(doc.querySelectorAll('link[rel="stylesheet"]')).map(link => 
            new URL(link.getAttribute('href'), cdnBaseUrl).href
        );
        
        styles.forEach(href => {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            document.head.appendChild(link);
        });
        
        await Promise.all(scripts.map(scriptObj => {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = scriptObj.src;
                script.type = scriptObj.type;
                script.onload = resolve;
                script.onerror = reject;
                document.body.appendChild(script);
            });
        }));
        
    } catch (error) {
        console.error('加载 Vue 应用失败:', error);
    }
}

// 当文档加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    appendFunction(vueAppLoader.cdnUrl);
}); 