(() => {
    'use strict';
    const config = JSON.parse(document.getElementById('ap-config').textContent);
    const s = config.settings;
    const days = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    const byId = id => document.getElementById(id);
    const field = (type, value, choices) => {
        const input = document.createElement(choices ? 'select' : 'input');
        if (choices) choices.forEach(([key, title]) => { const o = document.createElement('option'); o.value = key; o.textContent = title; input.append(o); });
        else input.type = type;
        input.value = value ?? '';
        if (type === 'number') { input.min = '1'; input.max = '1000'; }
        return input;
    };
    function row(type, data = {}) {
        const tr = document.createElement('tr');
        const fields = type === 'exceptions'
            ? [['date', field('date',data.date)], ['type',field('',data.type ?? 'both',[['both','الاستلام والتوصيل'],['pickup','الاستلام'],['delivery','التوصيل']])], ['start',field('time',data.start)], ['end',field('time',data.end)]]
            : [['day',field('',data.day ?? 0, days.map((d,i)=>[i,d]))], ['start',field('time',data.start ?? '09:00')], ['end',field('time',data.end ?? '11:00')], ['capacity',field('number',data.capacity ?? 5)]];
        fields.forEach(([key,input])=>{const td=document.createElement('td');input.dataset.key=key;td.append(input);tr.append(td);});
        const td=document.createElement('td'), remove=document.createElement('button');remove.type='button';remove.textContent='حذف';remove.className='ap-remove';remove.onclick=()=>tr.remove();td.append(remove);tr.append(td);byId('ap-'+type).append(tr);
    }
    byId('ap-enabled').checked=!!s.enabled;
    const unit=s.processing_minutes%1440===0?1440:s.processing_minutes%60===0?60:1;
    byId('ap-unit').value=unit;byId('ap-processing').value=s.processing_minutes/unit;byId('ap-horizon').value=s.horizon_days;
    for(const type of ['pickup','delivery']) {s.weekly[type].forEach(r=>row(type,r));}
    s.exceptions.forEach(r=>row('exceptions',r));
    document.querySelectorAll('.ap-add').forEach(button=>button.onclick=()=>row(button.dataset.type));
    byId('ap-add-exception').onclick=()=>row('exceptions');
    const read=type=>Array.from(byId('ap-'+type).rows).map(tr=>Object.fromEntries(Array.from(tr.querySelectorAll('[data-key]')).map(input=>[input.dataset.key,['day','capacity'].includes(input.dataset.key)?Number(input.value):input.value||null])));
    byId('ap-save').onclick=async()=>{
        const button=byId('ap-save'), status=byId('ap-status');button.disabled=true;status.textContent='جارٍ الحفظ…';
        try {
            const response=await fetch(config.url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':config.csrf},body:JSON.stringify({settings:{enabled:byId('ap-enabled').checked,processing_minutes:Number(byId('ap-processing').value)*Number(byId('ap-unit').value),horizon_days:Number(byId('ap-horizon').value),weekly:{pickup:read('pickup'),delivery:read('delivery')},exceptions:read('exceptions')}})});
            const result=await response.json();if(!response.ok)throw new Error(Object.values(result.errors??{}).flat().join('\n')||result.message||'تعذر الحفظ');status.textContent=result.message;
        }catch(error){status.textContent=error.message||'تعذر الاتصال. لم يتم حفظ الجدول.';}finally{button.disabled=false;}
    };
})();
