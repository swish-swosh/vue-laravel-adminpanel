

export default {
    methods: {
        getCurrentDateWithOffset(asString=false, offset = {}) {

            let wantedDate = new Date()

            Object.entries(offset).forEach(([key, value]) => {

                if(Number.isInteger(value)) {
                    switch(key){
                        case 'years':
                            wantedDate.setYear(wantedDate.getFullYear() + value)
                        break
                        case 'months':
                            wantedDate.setMonth(wantedDate.getMonth() + value)
                        break
                        case 'days':
                            wantedDate.setDate(wantedDate.getDate() + value )
                        break
                        case 'hours':
                            wantedDate.setHours(wantedDate.getHours()  + value)
                        break
                        case 'minutes':
                            wantedDate.setMinutes(wantedDate.getMinutes() + value)
                        break
                        case 'seconds':
                            wantedDate.setSeconds(wantedDate.getSeconds() + value)
                        break
                    }
                }
            });

            if(asString) return this.getISODateWithOffset(wantedDate)
            return wantedDate
        },
        calcDateTime(date, dateTime, action){
    
            switch (action) {
                case 'start':
                if (new Date(date).getTime() > new Date(dateTime.end.selected).getTime()) {
                    dateTime.start.error = true
                } else
                {
                    dateTime.start.error = false
                    dateTime.end.error = false
                    dateTime.start.selected = date
                }
                break
                case 'end':
                if (new Date(date).getTime() < new Date(dateTime.start.selected).getTime()) {
                    dateTime.end.error = true
                } else
                {
                    dateTime.start.error = false
                    dateTime.end.error = false
                    dateTime.end.selected = date
                }
                break
            }
        },
        getISODateWithOffset(dateString){

            let newDate = new Date(dateString)
            return new Date(newDate.getTime() - (newDate.getTimezoneOffset() * 60000)).toISOString();
        }
    }
}